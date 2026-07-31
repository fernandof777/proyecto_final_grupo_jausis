<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_trabajo_repuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos')->restrictOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->timestamps();

            $table->unique(['orden_trabajo_id', 'repuesto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_trabajo_repuesto');
    }
};
