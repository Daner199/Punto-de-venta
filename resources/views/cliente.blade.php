<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cliente · Punto de venta</title>
    <link rel="stylesheet" href="/css/estilos.css">
</head>
<!-- Datos de conexión a Reverb, tomados del .env a través de config/broadcasting.php.
     Van como atributos data-* y cliente.js los lee con document.body.dataset -->
<body class="pantalla-cliente"
      data-reverb-clave="{{ config('broadcasting.connections.reverb.key') }}"
      data-reverb-host="{{ config('broadcasting.connections.reverb.options.host') }}"
      data-reverb-puerto="{{ config('broadcasting.connections.reverb.options.port') }}"
      data-reverb-esquema="{{ config('broadcasting.connections.reverb.options.scheme') }}">

    <!-- Solo una de estas tres secciones se ve a la vez -->
    <section id="pantalla-esperando" class="escena escena-bienvenida">
        <h1>Bienvenido</h1>
        <p>En un momento lo atendemos.</p>
    </section>

    <section id="pantalla-en-curso" class="escena escena-compra" hidden>
        <h1>Su compra</h1>
        <table class="tabla-cliente">
            <thead>
                <tr><th>Producto</th><th>Cant.</th><th>Subtotal</th></tr>
            </thead>
            <tbody id="carrito"></tbody>
        </table>
        <div class="total-cliente">
            <span>Total</span>
            <span class="monto">Bs <span id="total">0.00</span></span>
        </div>
    </section>

    <section id="pantalla-agradeciendo" class="escena escena-gracias" hidden>
        <h1>Gracias por su compra</h1>
        <p>Vuelva pronto.</p>
        <!-- Barra que se vacía en 5 segundos, lo mismo que dura este mensaje -->
        <div class="cuenta-regresiva"><span></span></div>
    </section>

    <!-- Librerías para WebSockets: pusher-js (protocolo que usa Reverb) y Laravel Echo -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script src="/js/cliente.js"></script>
</body>
</html>
