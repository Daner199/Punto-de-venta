<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

// Se dispara cada vez que el carrito cambia. Lleva el estado COMPLETO,
// así la pantalla del cliente nunca depende de un evento anterior.
class CarritoActualizado implements ShouldBroadcastNow
{
    use Dispatchable;

    // Recibe el estado completo (productos, carrito, total y estado) y lo guarda en $this->estado
    public function __construct(public array $estado) {}

    // Canal público al que se suscribe la pantalla del cliente
    public function broadcastOn(): Channel
    {
        return new Channel('carrito');
    }

    // Nombre con el que lo escucha Echo
    public function broadcastAs(): string
    {
        return 'carrito.actualizado';
    }

    // Lo que viaja dentro del evento
    public function broadcastWith(): array
    {
        return $this->estado;
    }
}