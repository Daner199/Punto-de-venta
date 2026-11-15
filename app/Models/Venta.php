<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $table = 'ventas';
    public $timestamps = false;
    protected $fillable = ['estado', 'fecha'];
        // Le dice a Laravel que "fecha" es una fecha, para poder calcular con ella
    protected $casts = ['fecha' => 'datetime'];

    // Una venta tiene muchas líneas de detalle (FK venta_id en detalle_venta)
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    // total = suma de los subtotales   (lo que antes hacía vista_total_venta)
    public function total(): float
    {
        $total = 0;
        foreach ($this->detalles as $detalle) {
            $total = $total + $detalle->subtotal();
        }
        return $total;
    }

    // Busca la venta en curso; devuelve null si no hay ninguna
    public static function enCurso(): ?Venta
    {
        return Venta::where('estado', 'en_curso')->first();
    }
}