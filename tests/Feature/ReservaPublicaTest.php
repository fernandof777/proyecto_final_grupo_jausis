<?php

namespace Tests\Feature;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_displays_workshop_and_booking_form(): void
    {
        $response = $this->get(route('inicio'));

        $response->assertOk()
            ->assertSee('Grupo Los Jausis')
            ->assertSee('Reserva una cita para tu vehículo')
            ->assertSee('Acceso trabajadores');
    }

    public function test_customer_can_request_an_appointment_without_an_account(): void
    {
        $user = User::factory()->create();
        $servicio = $user->servicios()->create([
            'nombre' => 'Mantenimiento preventivo',
            'descripcion' => 'Cambio de aceite y revisión general.',
            'precio' => 250,
            'duracion_estimada' => 90,
            'estado' => 'Activo',
        ]);

        $response = $this->post(route('reservas.store'), [
            'nombre' => 'María Pérez',
            'telefono' => '70000000',
            'email' => 'maria@example.com',
            'servicio_id' => $servicio->id,
            'vehiculo_marca' => 'Toyota',
            'vehiculo_modelo' => 'Corolla',
            'placa' => '1234abc',
            'fecha_preferida' => now()->addDays(2)->toDateString(),
            'hora_preferida' => '10:30',
            'mensaje' => 'Necesito mantenimiento general.',
        ]);

        $response->assertRedirect(route('inicio').'#reservar')
            ->assertSessionHas('reserva_exitosa');
        $this->assertDatabaseHas('reservas', [
            'nombre' => 'María Pérez',
            'placa' => '1234ABC',
            'estado' => 'Pendiente',
            'servicio_id' => $servicio->id,
        ]);
    }

    public function test_booking_rejects_a_past_date(): void
    {
        $this->post(route('reservas.store'), [
            'nombre' => 'Cliente de prueba',
            'telefono' => '70000001',
            'vehiculo_marca' => 'Nissan',
            'fecha_preferida' => now()->subDay()->toDateString(),
            'hora_preferida' => '09:00',
        ])->assertSessionHasErrors('fecha_preferida');

        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_booking_rejects_a_time_outside_business_hours(): void
    {
        $this->post(route('reservas.store'), [
            'nombre' => 'Cliente de prueba',
            'telefono' => '70000004',
            'vehiculo_marca' => 'Suzuki',
            'fecha_preferida' => now()->addDay()->toDateString(),
            'hora_preferida' => '22:00',
        ])->assertSessionHasErrors('hora_preferida');

        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_only_reception_and_admin_can_manage_bookings(): void
    {
        $recepcionista = User::factory()->create(['role' => 'recepcionista']);
        $almacen = User::factory()->create(['role' => 'almacen']);
        $reserva = Reserva::create([
            'codigo' => 'CITA-TEST-01',
            'nombre' => 'Cliente',
            'telefono' => '70000002',
            'vehiculo_marca' => 'Kia',
            'fecha_preferida' => now()->addDay()->toDateString(),
            'hora_preferida' => '11:00',
        ]);

        $this->get(route('reservas.index'))->assertRedirect(route('login'));
        $this->actingAs($almacen)->get(route('reservas.index'))->assertForbidden();
        $this->actingAs($recepcionista)->get(route('reservas.index'))->assertOk()->assertSee('CITA-TEST-01');
        $this->actingAs($recepcionista)->patch(route('reservas.update', $reserva), [
            'estado' => 'Confirmada',
            'nota_interna' => 'Cliente contactado.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'estado' => 'Confirmada',
            'nota_interna' => 'Cliente contactado.',
        ]);
    }

    public function test_only_admin_can_delete_a_booking(): void
    {
        $recepcionista = User::factory()->create(['role' => 'recepcionista']);
        $admin = User::factory()->create(['role' => 'admin']);
        $reserva = Reserva::create([
            'codigo' => 'CITA-TEST-02',
            'nombre' => 'Cliente',
            'telefono' => '70000003',
            'vehiculo_marca' => 'Ford',
            'fecha_preferida' => now()->addDay()->toDateString(),
            'hora_preferida' => '15:00',
        ]);

        $this->actingAs($recepcionista)->delete(route('reservas.destroy', $reserva))->assertForbidden();
        $this->actingAs($admin)->delete(route('reservas.destroy', $reserva))->assertRedirect();
        $this->assertDatabaseMissing('reservas', ['id' => $reserva->id]);
    }
}
