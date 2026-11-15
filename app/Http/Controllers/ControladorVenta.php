<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\CarritoActualizado;

class ControladorVenta extends Controller
{
    // Agrega un producto al carrito. Si ya está, le suma 1 a la cantidad.
    public function agregarProducto(Request $peticion): JsonResponse
    {
        $datos = $peticion->validate([
            'producto_id' => 'required|integer|exists:productos,id',
        ]);

        $producto = Producto::find($datos['producto_id']);

        // Si no hay venta en curso, se crea una nueva
        $venta = Venta::enCurso();
        if ($venta === null) {
            $venta = Venta::create(['estado' => 'en_curso', 'fecha' => now()]);
        }

        // ¿El producto ya está en el carrito?
        $detalle = DetalleVenta::where('venta_id', $venta->id)
            ->where('producto_id', $producto->id)
            ->first();

        if ($detalle !== null) {
            // Ya estaba → sumamos 1 (no duplicamos la línea)
            $detalle->cantidad = $detalle->cantidad + 1;
            $detalle->save();
        } else {
            // No estaba → nueva línea, copiando el precio de hoy a precio_cobrado
            DetalleVenta::create([
                'venta_id'       => $venta->id,
                'producto_id'    => $producto->id,
                'cantidad'       => 1,
                'precio_cobrado' => $producto->precio_actual,
            ]);
        }

        return $this->responderConEstado();
    }

    // Cambia la cantidad de un producto. Si llega 0 o menos, lo quita.
    public function cambiarCantidad(Request $peticion): JsonResponse
    {
        $datos = $peticion->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'cantidad'    => 'required|integer',          // debe ser un número entero
        ]);

        $venta = Venta::enCurso();
        if ($venta === null) {
            return response()->json(['error' => 'No hay una venta en curso'], 400);
        }

        $detalle = DetalleVenta::where('venta_id', $venta->id)
            ->where('producto_id', $datos['producto_id'])
            ->first();

        if ($detalle === null) {
            return response()->json(['error' => 'Ese producto no está en el carrito'], 400);
        }

        if ($datos['cantidad'] <= 0) {
            // Caso borde: cantidad en cero → se quita el producto
            $this->quitarLinea($venta, $detalle);
        } else {
            $detalle->cantidad = $datos['cantidad'];
            $detalle->save();
        }

        return $this->responderConEstado();
    }

    // Quita un producto del carrito
    public function quitarProducto(Request $peticion): JsonResponse
    {
        $datos = $peticion->validate([
            'producto_id' => 'required|integer|exists:productos,id',
        ]);

        $venta = Venta::enCurso();
        if ($venta === null) {
            return response()->json(['error' => 'No hay una venta en curso'], 400);
        }

        $detalle = DetalleVenta::where('venta_id', $venta->id)
            ->where('producto_id', $datos['producto_id'])
            ->first();

        if ($detalle !== null) {
            $this->quitarLinea($venta, $detalle);
        }

        return $this->responderConEstado();
    }

    // Finaliza la venta: queda registrada y la pantalla del cliente agradece
    public function finalizarVenta(): JsonResponse
    {
        $venta = Venta::enCurso();

        // Caso borde: carrito vacío → no se puede finalizar
        if ($venta === null || $venta->detalles()->count() === 0) {
            return response()->json(['error' => 'No hay productos para finalizar'], 400);
        }

        $venta->estado = 'finalizada';
        $venta->fecha  = now();          // hora exacta en que se cerró la venta
        $venta->save();

        // Se avisa "finalizada" para que el cliente muestre "Gracias" antes de volver a "Bienvenido"
        return $this->responderConEstado('finalizada');
    }

    // Cancela la venta en curso: el cliente vuelve a "Bienvenido" sin agradecer
    public function cancelarVenta(): JsonResponse
    {
        $venta = Venta::enCurso();

        if ($venta !== null) {
            $venta->estado = 'cancelada';
            $venta->save();
        }

        return $this->responderConEstado();
    }

    // Arma el estado, lo envía por WebSocket a la pantalla del cliente y lo devuelve al cajero.
    // Si se indica $estadoForzado (por ejemplo 'finalizada'), reemplaza al estado calculado.
    private function responderConEstado(?string $estadoForzado = null): JsonResponse
    {
        $estado = ControladorEstado::estadoActual();

        if ($estadoForzado !== null) {
            $estado['estado'] = $estadoForzado;
        }

        CarritoActualizado::dispatch($estado);

        return response()->json($estado);
    }

    // Función de ayuda: borra una línea y, si el carrito queda vacío, cancela la venta
    private function quitarLinea(Venta $venta, DetalleVenta $detalle): void
    {
        $detalle->delete();

        if ($venta->detalles()->count() === 0) {
            $venta->estado = 'cancelada';
            $venta->save();
        }
    }
}