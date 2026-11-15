# Punto de venta con pantalla del cliente

## Qué es esto

Es la prueba tecnica. Es un punto de venta con dos pantallas: la del cajero, donde se arma la venta, y la del cliente, que muestra el carrito en vivo mientras el cajero agrega productos.

Está hecho con Laravel 12 y PostgreSQL. El JavaScript está en `public/js/` y las librerías de WebSockets (Laravel Echo y pusher-js) se cargan por CDN( Red de Entrega de Contenidos).

La base tiene tres tablas: `productos`, `ventas` y `detalle_venta`.
## Qué necesitas tener instalado

- PHP 8.2 o superior
- Composer
- PostgreSQL

## Cómo levantarlo

1. Clona el repositorio y entra a la carpeta.
2. `composer install`
3. Copia `.env.example` a `.env`.
4. En el `.env`, pon los datos de tu base en las variables `DB_*` y llena `REVERB_APP_ID`, `REVERB_APP_KEY` y `REVERB_APP_SECRET` con cualquier valor. Las demás `REVERB_*` ya vienen listas para local.
5. `php artisan key:generate`
6. `php artisan migrate --seed` (crea las tablas y carga unos productos de prueba)

Después necesitas dos terminales abiertas:

- `php artisan serve`
- `php artisan reverb:start`

Con eso abres `http://localhost:8000/cajero` en una ventana y `http://localhost:8000/cliente` en otra.

## Cómo se comunican las dos pantallas

El cajero manda cada acción (agregar, cambiar cantidad, quitar, finalizar, cancelar) con un POST normal. El servidor guarda el cambio y responde con el estado actualizado. En ese mismo momento dispara el evento `CarritoActualizado`, que sale por WebSocket.

La pantalla del cliente hace dos cosas. Primero pide el estado por HTTP a `/api/estado`, por si se abre cuando la venta ya empezó; así no queda en "Bienvenido" con un carrito a medias. Después se queda escuchando el canal `carrito` con Echo.

Cada evento manda el carrito completo (productos, líneas, total y estado), no solo lo que cambió. Me pareció más seguro: si se pierde un evento, el siguiente igual trae todo, y la pantalla nunca queda desfasada.

## Por qué WebSockets y no polling

Empecé con polling porque era lo más simple: el cliente preguntaba al servidor cada pocos milisegundos. Funcionaba, pero lo cambié a Reverb por dos razones: el cliente ve los cambios al instante y el servidor ya no recibe un montón de peticiones que casi siempre devuelven lo mismo.


## Casos borde que tuve en cuenta

Si el cajero pone la cantidad en 0, el producto se quita del carrito.

Si el carrito está vacío, los botones Finalizar y Cancelar quedan desactivados, y el servidor también rechaza finalizar una venta sin productos.

Si se quita el último producto, la venta se cancela sola y el cliente vuelve a "Bienvenido".

Si la pantalla del cliente se abre tarde, muestra el carrito actual gracias al GET inicial.

Al finalizar, el cliente muestra "Gracias" 5 segundos con un `setTimeout`. Si en ese tiempo el cajero ya empieza otra venta, hago `clearTimeout` para que la pantalla no salte a "Bienvenido" con un carrito activo.


