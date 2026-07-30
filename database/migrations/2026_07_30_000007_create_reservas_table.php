<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 24)->unique();
            $table->string('nombre', 120);
            $table->string('telefono', 30);
            $table->string('email')->nullable();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->string('vehiculo_marca', 80);
            $table->string('vehiculo_modelo', 80)->nullable();
            $table->string('placa', 20)->nullable();
            $table->date('fecha_preferida');
            $table->string('hora_preferida', 5);
            $table->text('mensaje')->nullable();
            $table->string('estado', 30)->default('Pendiente');
            $table->text('nota_interna')->nullable();
            $table->timestamps();

            $table->index(['estado', 'fecha_preferida']);
            $table->index('telefono');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
