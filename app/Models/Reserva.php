<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'telefono',
        'email',
        'servicio_id',
        'vehiculo_marca',
        'vehiculo_modelo',
        'placa',
        'fecha_preferida',
        'hora_preferida',
        'mensaje',
        'estado',
        'nota_interna',
    ];

    protected function casts(): array
    {
        return [
            'fecha_preferida' => 'date',
        ];
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}
