<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caja · Punto de venta</title>
    <link rel="stylesheet" href="/css/estilos.css">
</head>
<body class="pantalla-cajero">
    <!-- Barra superior -->
    <header class="barra">
        <strong>Caja</strong>
        <a href="/cliente" target="_blank">Abrir pantalla del cliente ↗</a>
    </header>

    <main class="columnas">
        <!-- Columna izquierda: productos disponibles -->
        <section>
            <h2>Productos</h2>
            <div id="lista-productos" class="productos"></div>
        </section>

        <!-- Columna derecha: el carrito -->
        <section class="carrito-panel">
            <h2>Venta actual</h2>

            <table class="tabla-carrito">
                <thead>
                    <tr><th>Producto</th><th>Precio</th><th>Cant.</th><th>Subtotal</th><th></th></tr>
                </thead>
                <tbody id="carrito"></tbody>
            </table>
            <!-- Se muestra solo cuando el carrito está vacío (lo controla el CSS) -->
            <p class="vacio">Sin productos. Elige uno de la lista para empezar.</p>

            <div class="total-linea">
                <span>Total</span>
                <span class="monto">Bs <span id="total">0.00</span></span>
            </div>

            <div class="acciones">
                <button id="boton-finalizar" class="boton principal" disabled>Finalizar venta</button>
                <button id="boton-cancelar" class="boton rojo" disabled>Cancelar</button>
            </div>
            <p id="mensaje" class="mensaje"></p>
        </section>
    </main>

    <script src="/js/cajero.js"></script>
</body>
</html>
