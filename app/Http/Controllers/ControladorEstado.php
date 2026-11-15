<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;

class ControladorEstado extends Controller
{
    // Devuelve el estado COMPLETO: lista de productos + carrito actual.
    // Las dos pantallas lo piden al abrirse, para mostrar cómo está la venta en ese momento.
    public function obtener(): JsonResponse
    {
        return response()->json(self::estadoActual());
    }

    // Arma el estado como un arreglo. Es "static" para que ControladorVenta
    // también pueda usarlo y devolver el mismo formato después de cada cambio.
    public static function estadoActual(): array
    {
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'precio_actual']);
        $venta = Venta::enCurso();

        // CASO 1: hay una venta en curso → se manda el carrito completo
        if ($venta !== null) {
            $lineas = [];
            foreach ($venta->detalles as $detalle) {
                $lineas[] = [
                    'producto_id'    => $detalle->producto_id,
                    'nombre'         => $detalle->producto->nombre,
                    'precio_cobrado' => $detalle->precio_cobrado,
                    'cantidad'       => $detalle->cantidad,
                    'subtotal'       => $detalle->subtotal(),
                ];
            }

            return [
                'estado'    => 'en_curso',
                'productos' => $productos,
                'carrito'   => $lineas,
                'total'     => $venta->total(),
            ];
        }

        // CASO 2: no hay venta en curso → la pantalla del cliente muestra "Bienvenido"
        return [
            'estado'    => 'esperando',
            'productos' => $productos,
            'carrito'   => [],
            'total'     => 0,
        ];
    }
}