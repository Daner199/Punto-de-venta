<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';
    public $timestamps = false;
    protected $fillable = ['venta_id', 'producto_id', 'cantidad', 'precio_cobrado'];

    // Cada línea del detalle pertenece a un producto (FK producto_id)
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // subtotal = cantidad × precio_cobrado   (lo que antes hacía vista_detalle_venta)
    public function subtotal(): float
    {
        return $this->cantidad * $this->precio_cobrado;
    }
}