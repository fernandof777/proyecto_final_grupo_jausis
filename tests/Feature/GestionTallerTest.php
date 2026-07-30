<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Repuesto;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDOException;
use Tests\TestCase;

class GestionTallerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_client_vehicle_and_order(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Ana Pérez', 'ci_nit' => '1234567', 'telefono' => '70000000',
            'email' => 'ana@example.com', 'ciudad' => 'Santa Cruz', 'direccion' => '', 'activo' => 1,
        ])->assertRedirect(route('clientes.index'));

        $cliente = Cliente::first();
        $this->actingAs($user)->post(route('vehiculos.store'), [
            'cliente_id' => $cliente->id, 'placa' => '1234-XYZ', 'marca' => 'Nissan',
            'modelo' => 'Versa', 'anio' => 2022, 'color' => 'Gris', 'kilometraje' => 24000,
        ])->assertRedirect(route('vehiculos.index'));

        $vehiculo = Vehiculo::first();
        $this->actingAs($user)->post(route('ordenes.store'), [
            'cliente_id' => $cliente->id, 'vehiculo_id' => $vehiculo->id,
            'problema' => 'Vibración al frenar', 'diagnostico' => '',
            'estado' => 'Pendiente', 'fecha_ingreso' => now()->toDateString(),
            'fecha_entrega_estimada' => now()->addDay()->toDateString(), 'fecha_entrega' => '', 'total' => 0,
        ])->assertRedirect(route('ordenes.index'));

        $this->assertDatabaseCount('clientes', 1);
        $this->assertDatabaseCount('vehiculos', 1);
        $this->assertDatabaseHas('ordenes_trabajo', ['cliente_id' => $cliente->id, 'vehiculo_id' => $vehiculo->id, 'user_id' => $user->id]);
    }

    public function test_order_rejects_vehicle_from_another_client(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create(['nombre' => 'Uno', 'ci_nit' => '1', 'telefono' => '1', 'ciudad' => 'SC', 'activo' => true]);
        $otro = Cliente::create(['nombre' => 'Dos', 'ci_nit' => '2', 'telefono' => '2', 'ciudad' => 'SC', 'activo' => true]);
        $vehiculo = Vehiculo::create(['cliente_id' => $otro->id, 'placa' => 'OTRO', 'marca' => 'Kia', 'modelo' => 'Rio', 'anio' => 2020, 'kilometraje' => 10]);

        $this->actingAs($user)->post(route('ordenes.store'), [
            'cliente_id' => $cliente->id, 'vehiculo_id' => $vehiculo->id, 'problema' => 'Prueba',
            'estado' => 'Pendiente', 'fecha_ingreso' => now()->toDateString(), 'total' => 0,
        ])->assertSessionHasErrors('vehiculo_id');

        $this->assertDatabaseCount('ordenes_trabajo', 0);
    }

    public function test_new_order_rejects_a_past_entry_date(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $cliente = Cliente::create(['nombre' => 'Cliente', 'ci_nit' => 'F-1', 'telefono' => '70000000', 'ciudad' => 'SC', 'activo' => true]);
        $vehiculo = Vehiculo::create(['cliente_id' => $cliente->id, 'placa' => 'FECHA-1', 'marca' => 'Toyota', 'modelo' => 'Yaris', 'anio' => 2022, 'kilometraje' => 100]);

        $this->actingAs($user)->post(route('ordenes.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'problema' => 'Revisión general',
            'estado' => 'Pendiente',
            'fecha_ingreso' => now()->subDay()->toDateString(),
            'total' => 0,
        ])->assertSessionHasErrors('fecha_ingreso');

        $this->assertDatabaseCount('ordenes_trabajo', 0);
    }

    public function test_order_dates_accept_same_day_and_reject_delivery_before_entry(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $cliente = Cliente::create(['nombre' => 'Cliente fechas', 'ci_nit' => 'F-2', 'telefono' => '70000001', 'ciudad' => 'SC', 'activo' => true]);
        $vehiculo = Vehiculo::create(['cliente_id' => $cliente->id, 'placa' => 'FECHA-2', 'marca' => 'Nissan', 'modelo' => 'Versa', 'anio' => 2021, 'kilometraje' => 100]);
        $hoy = now()->toDateString();

        $this->actingAs($user)->post(route('ordenes.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'problema' => 'Mantenimiento',
            'estado' => 'Pendiente',
            'fecha_ingreso' => $hoy,
            'fecha_entrega_estimada' => $hoy,
            'total' => 0,
        ])->assertRedirect(route('ordenes.index'));

        $this->actingAs($user)->post(route('ordenes.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'problema' => 'Segunda revisión',
            'estado' => 'Pendiente',
            'fecha_ingreso' => now()->addDay()->toDateString(),
            'fecha_entrega_estimada' => $hoy,
            'total' => 0,
        ])->assertSessionHasErrors('fecha_entrega_estimada');

        $this->assertDatabaseCount('ordenes_trabajo', 1);
    }

    public function test_updating_order_rejects_vehicle_from_another_client(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $cliente = Cliente::create(['nombre' => 'Titular', 'ci_nit' => 'REL-1', 'telefono' => '70000002', 'ciudad' => 'SC', 'activo' => true]);
        $otro = Cliente::create(['nombre' => 'Otro', 'ci_nit' => 'REL-2', 'telefono' => '70000003', 'ciudad' => 'SC', 'activo' => true]);
        $vehiculo = Vehiculo::create(['cliente_id' => $cliente->id, 'placa' => 'REL-A', 'marca' => 'Kia', 'modelo' => 'Rio', 'anio' => 2020, 'kilometraje' => 10]);
        $vehiculoAjeno = Vehiculo::create(['cliente_id' => $otro->id, 'placa' => 'REL-B', 'marca' => 'Ford', 'modelo' => 'Ka', 'anio' => 2019, 'kilometraje' => 10]);
        $orden = $user->ordenesTrabajo()->create([
            'numero' => 'OT-REL-01',
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'problema' => 'Prueba',
            'estado' => 'Pendiente',
            'fecha_ingreso' => now()->toDateString(),
            'total' => 0,
        ]);

        $this->actingAs($user)->put(route('ordenes.update', $orden), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculoAjeno->id,
            'problema' => 'Prueba',
            'estado' => 'Pendiente',
            'fecha_ingreso' => now()->toDateString(),
            'total' => 0,
        ])->assertSessionHasErrors('vehiculo_id');

        $this->assertDatabaseHas('ordenes_trabajo', [
            'id' => $orden->id,
            'vehiculo_id' => $vehiculo->id,
        ]);
    }

    public function test_client_database_error_returns_a_friendly_message(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Cliente::creating(function (): void {
            throw new QueryException(
                'sqlite',
                'insert into clientes',
                [],
                new PDOException('Restricción de base de datos'),
            );
        });

        $this->actingAs($user)
            ->from(route('clientes.create'))
            ->post(route('clientes.store'), [
                'nombre' => 'Cliente inválido',
                'ci_nit' => 'DB-ERROR',
                'telefono' => '70000004',
                'email' => 'cliente@example.com',
                'ciudad' => 'Santa Cruz',
                'direccion' => null,
                'activo' => 1,
            ])
            ->assertRedirect(route('clientes.create'))
            ->assertSessionHas('error', 'No se pudo registrar el cliente. Verifica que los datos sean válidos e inténtalo nuevamente.');

        $this->assertDatabaseCount('clientes', 0);
    }

    public function test_client_data_is_normalized_and_duplicate_ci_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => '  Ana Pérez  ',
            'ci_nit' => 'abc-123',
            'telefono' => ' 70000005 ',
            'email' => ' ANA@EXAMPLE.COM ',
            'ciudad' => ' Santa Cruz ',
            'direccion' => ' Centro ',
            'activo' => 1,
        ])->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Ana Pérez',
            'ci_nit' => 'ABC-123',
            'telefono' => '70000005',
            'email' => 'ana@example.com',
            'ciudad' => 'Santa Cruz',
            'direccion' => 'Centro',
        ]);

        $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Otro cliente',
            'ci_nit' => 'abc-123',
            'telefono' => '70000006',
            'ciudad' => 'Santa Cruz',
            'activo' => 1,
        ])->assertSessionHasErrors('ci_nit');

        $this->assertDatabaseCount('clientes', 1);
    }

    public function test_inventory_and_reports_are_available(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Repuesto::create(['codigo' => 'R-1', 'nombre' => 'Filtro', 'stock' => 2, 'stock_minimo' => 5, 'precio' => 50, 'activo' => true]);

        $this->actingAs($user)->get(route('repuestos.index', ['alerta' => 'stock']))->assertOk()->assertSee('Filtro');
        $this->actingAs($user)->get(route('reportes.index'))->assertOk()->assertSee('Stock bajo');
    }

    public function test_admin_can_download_filtered_report_as_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cliente = Cliente::create([
            'nombre' => 'Cliente PDF',
            'ci_nit' => 'PDF-1',
            'telefono' => '70000001',
            'ciudad' => 'Santa Cruz',
            'activo' => true,
        ]);
        $vehiculo = Vehiculo::create([
            'cliente_id' => $cliente->id,
            'placa' => 'PDF-001',
            'marca' => 'Toyota',
            'modelo' => 'Yaris',
            'anio' => 2023,
            'kilometraje' => 12000,
        ]);
        $admin->ordenesTrabajo()->create([
            'numero' => 'OT-PDF-001',
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'problema' => 'Mantenimiento preventivo',
            'estado' => 'Entregada',
            'fecha_ingreso' => '2026-07-15',
            'total' => 450,
        ]);

        $response = $this->actingAs($admin)->get(route('reportes.pdf', [
            'desde' => '2026-07-01',
            'hasta' => '2026-07-31',
        ]));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename="informe-taller-2026-07-01_2026-07-31.pdf"');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_client_with_vehicle_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create(['nombre' => 'Cliente', 'ci_nit' => '10', 'telefono' => '1', 'ciudad' => 'SC', 'activo' => true]);
        Vehiculo::create(['cliente_id' => $cliente->id, 'placa' => 'ABC', 'marca' => 'Ford', 'modelo' => 'Ka', 'anio' => 2019, 'kilometraje' => 0]);

        $this->actingAs($user)->delete(route('clientes.destroy', $cliente))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
    }

    public function test_authenticated_user_can_view_workshop_location(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('ubicacion.index'))
            ->assertOk()
            ->assertSee('-17.7592893')
            ->assertSee('6RRG+77G');
    }
}
