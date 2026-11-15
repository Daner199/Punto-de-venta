// ---------- Referencias a los elementos de la pantalla ----------
const listaProductos = document.getElementById('lista-productos');
const cuerpoCarrito  = document.getElementById('carrito');
const textoTotal     = document.getElementById('total');
const botonFinalizar = document.getElementById('boton-finalizar');
const botonCancelar  = document.getElementById('boton-cancelar');
const mensaje        = document.getElementById('mensaje');

// ---------- Hablar con el servidor ----------

// Pide el estado completo (GET /api/estado)
async function obtenerEstado() {
    const respuesta = await fetch('/api/estado');
    return await respuesta.json();
}

// Manda una acción al servidor (POST) y devuelve el nuevo estado
async function enviarAccion(ruta, datos = {}) {
    const respuesta = await fetch(ruta, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(datos),
    });
    const estado = await respuesta.json();

    if (!respuesta.ok) {
        // El servidor devolvió un error (por ejemplo, carrito vacío)
        mensaje.textContent = estado.error ?? 'Ocurrió un error';
        return null;
    }

    mensaje.textContent = '';
    return estado;
}

// ---------- Dibujar en pantalla ----------

// Dibuja un botón por cada producto disponible; al hacer clic lo agrega al carrito
function dibujarProductos(productos) {
    listaProductos.innerHTML = '';
    for (const producto of productos) {
        const boton = document.createElement('button');
        boton.className = 'producto';
        boton.innerHTML = `<strong></strong><span class="precio">Bs ${Number(producto.precio_actual).toFixed(2)}</span>`;
        // El nombre va con textContent para que se muestre como texto y nunca como HTML
        boton.querySelector('strong').textContent = producto.nombre;
        boton.onclick = () => agregarProducto(producto.id);
        listaProductos.appendChild(boton);
    }
}

// Dibuja las líneas del carrito (con cantidad editable y botón Quitar), el total y los botones
function dibujarCarrito(estado) {
    cuerpoCarrito.innerHTML = '';

    for (const linea of estado.carrito) {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td></td>
            <td>Bs ${Number(linea.precio_cobrado).toFixed(2)}</td>
            <td><input type="number" min="0" value="${linea.cantidad}"></td>
            <td>Bs ${Number(linea.subtotal).toFixed(2)}</td>
            <td><button class="boton rojo chico">Quitar</button></td>
        `;
        // El nombre va con textContent para que se muestre como texto y nunca como HTML
        fila.querySelector('td').textContent = linea.nombre;
        // Al cambiar el número de la cantidad
        fila.querySelector('input').onchange = (evento) => cambiarCantidad(linea.producto_id, evento.target.value);
        // Al hacer clic en "Quitar"
        fila.querySelector('button').onclick = () => quitarProducto(linea.producto_id);
        cuerpoCarrito.appendChild(fila);
    }

    textoTotal.textContent = Number(estado.total).toFixed(2);

    // Caso borde: carrito vacío → no se puede finalizar ni cancelar
    const carritoVacio = estado.carrito.length === 0;
    botonFinalizar.disabled = carritoVacio;
    botonCancelar.disabled  = carritoVacio;
}

// Dibuja todo a partir de un estado
function dibujarTodo(estado) {
    if (estado === null) return;
    dibujarProductos(estado.productos);
    dibujarCarrito(estado);
}

// ---------- Acciones del cajero ----------

// Pide al servidor agregar un producto (o sumarle 1) y redibuja
async function agregarProducto(productoId) {
    dibujarTodo(await enviarAccion('/api/venta/agregar', { producto_id: productoId }));
}

// Pide al servidor cambiar la cantidad (0 quita el producto) y redibuja
async function cambiarCantidad(productoId, cantidad) {
    dibujarTodo(await enviarAccion('/api/venta/cantidad', { producto_id: productoId, cantidad: Number(cantidad) }));
}

// Pide al servidor quitar un producto del carrito y redibuja
async function quitarProducto(productoId) {
    dibujarTodo(await enviarAccion('/api/venta/quitar', { producto_id: productoId }));
}

// Botones Finalizar y Cancelar: avisan al servidor y redibujan con la respuesta
botonFinalizar.onclick = async () => dibujarTodo(await enviarAccion('/api/venta/finalizar'));
botonCancelar.onclick  = async () => dibujarTodo(await enviarAccion('/api/venta/cancelar'));

// ---------- Al abrir la página ----------
obtenerEstado().then(dibujarTodo);