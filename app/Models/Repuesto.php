<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $fillable = ['codigo', 'nombre', 'proveedor', 'stock', 'stock_minimo', 'precio', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'precio' => 'decimal:2'];
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
