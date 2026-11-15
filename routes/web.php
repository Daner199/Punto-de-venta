<?php

use App\Http\Controllers\ControladorEstado;
use App\Http\Controllers\ControladorPantallas;
use App\Http\Controllers\ControladorVenta;
use Illuminate\Support\Facades\Route;

// ---------- Pantallas (lo que ve la gente) ----------
Route::get('/', [ControladorPantallas::class, 'inicio']);          // página de inicio con los dos enlaces
Route::get('/cajero', [ControladorPantallas::class, 'cajero']);    // pantalla del cajero
Route::get('/cliente', [ControladorPantallas::class, 'cliente']);  // pantalla del cliente

// ---------- API (lo que usan las pantallas por detrás, responde JSON) ----------
Route::get('/api/estado', [ControladorEstado::class, 'obtener']);  // estado completo del carrito (carga inicial)

Route::post('/api/venta/agregar',   [ControladorVenta::class, 'agregarProducto']);
Route::post('/api/venta/cantidad',  [ControladorVenta::class, 'cambiarCantidad']);
Route::post('/api/venta/quitar',    [ControladorVenta::class, 'quitarProducto']);
Route::post('/api/venta/finalizar', [ControladorVenta::class, 'finalizarVenta']);
Route::post('/api/venta/cancelar',  [ControladorVenta::class, 'cancelarVenta']);