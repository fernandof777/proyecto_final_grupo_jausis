<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenTrabajo extends Model
{
    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'numero', 'cliente_id', 'vehiculo_id', 'problema', 'diagnostico', 'estado',
        'fecha_ingreso', 'fecha_entrega_estimada', 'fecha_entrega', 'total',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'fecha_entrega_estimada' => 'date',
            'fecha_entrega' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
