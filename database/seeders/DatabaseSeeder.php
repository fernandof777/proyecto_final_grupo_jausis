<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Repuesto;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'luis@taller.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password123');

        $luis = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Luis Fernando',
                'password' => Hash::make($adminPassword),
            ]
        );
        $luis->forceFill(['role' => 'admin', 'activo' => true])->save();

        $maria = User::firstOrCreate(
            ['email' => 'maria@taller.com'],
            [
                'name' => 'Maria Garcia',
                'password' => Hash::make('password123'),
            ]
        );
        $maria->forceFill(['role' => 'recepcionista', 'activo' => true])->save();

        $cliente = Cliente::firstOrCreate(
            ['ci_nit' => '7845123'],
            ['nombre' => 'Carlos Mendoza', 'telefono' => '70012345', 'email' => 'carlos@example.com', 'ciudad' => 'Santa Cruz', 'direccion' => 'Av. Mutualista 120', 'activo' => true]
        );

        $vehiculo = Vehiculo::firstOrCreate(
            ['placa' => '4821-ABC'],
            ['cliente_id' => $cliente->id, 'marca' => 'Toyota', 'modelo' => 'Corolla', 'anio' => 2020, 'color' => 'Blanco', 'kilometraje' => 68500]
        );

        Repuesto::firstOrCreate(
            ['codigo' => 'FIL-001'],
            ['nombre' => 'Filtro de aceite', 'proveedor' => 'AutoPartes SRL', 'stock' => 4, 'stock_minimo' => 5, 'precio' => 65, 'activo' => true]
        );

        Repuesto::firstOrCreate(
            ['codigo' => 'ACE-5W30'],
            ['nombre' => 'Aceite sintético 5W-30', 'proveedor' => 'Lubricantes Bolivia', 'stock' => 18, 'stock_minimo' => 6, 'precio' => 95, 'activo' => true]
        );

        $luis->ordenesTrabajo()->firstOrCreate(
            ['numero' => 'OT-DEMO-001'],
            [
                'cliente_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->id,
                'problema' => 'Ruido al frenar y mantenimiento preventivo.',
                'diagnostico' => 'Pastillas delanteras desgastadas.',
                'estado' => 'En reparación',
                'fecha_ingreso' => now()->toDateString(),
                'fecha_entrega_estimada' => now()->addDay()->toDateString(),
                'total' => 480,
            ]
        );
    }
}
