<?php

namespace App\Http\Controllers;

class ControladorPantallas extends Controller
{
    // Muestra la página de inicio con los enlaces a las dos pantallas
    public function inicio()
    {
        return view('inicio');
    }

    // Muestra la pantalla del cajero, donde se registra la venta
    public function cajero()
    {
        return view('cajero');
    }

    // Muestra la pantalla del cliente, que ve el carrito en vivo
    public function cliente()
    {
        return view('cliente');
    }
}
