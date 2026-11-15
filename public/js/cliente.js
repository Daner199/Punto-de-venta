// Milisegundos que se muestra "Gracias" después de finalizar la venta
const DURACION_AGRADECIMIENTO = 5000;

const pantallaEsperando    = document.getElementById('pantalla-esperando');
const pantallaEnCurso      = document.getElementById('pantalla-en-curso');
const pantallaAgradeciendo = document.getElementById('pantalla-agradeciendo');
const cuerpoCarrito        = document.getElementById('carrito');
const textoTotal           = document.getElementById('total');

// Guarda el temporizador de "Gracias" para poder anularlo si empieza otra venta
let temporizadorAgradecimiento = null;

// Muestra una sola pantalla y oculta las otras dos
function mostrarPantalla(cual) {
    pantallaEsperando.hidden    = (cual !== 'esperando');
    pantallaEnCurso.hidden      = (cual !== 'en_curso');
    pantallaAgradeciendo.hidden = (cual !== 'agradeciendo');
}

// Dibuja las líneas del carrito y el total
function dibujarCarrito(estado) {
    cuerpoCarrito.innerHTML = '';
    for (const linea of estado.carrito) {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td></td>
            <td>${linea.cantidad}</td>
            <td>Bs ${Number(linea.subtotal).toFixed(2)}</td>
        `;
        // El nombre va con textContent para que se muestre como texto y nunca como HTML
        fila.querySelector('td').textContent = linea.nombre;
        cuerpoCarrito.appendChild(fila);
    }
    textoTotal.textContent = Number(estado.total).toFixed(2);
}

// Decide qué mostrar según el estado que mandó el servidor
function aplicarEstado(estado) {
    // Si llega cualquier estado nuevo, se anula un "Gracias" pendiente
    clearTimeout(temporizadorAgradecimiento);

    if (estado.estado === 'en_curso') {
        dibujarCarrito(estado);
        mostrarPantalla('en_curso');
    } else if (estado.estado === 'finalizada') {
        // Venta finalizada: "Gracias" 5 segundos y después "Bienvenido"
        mostrarPantalla('agradeciendo');
        temporizadorAgradecimiento = setTimeout(() => mostrarPantalla('esperando'), DURACION_AGRADECIMIENTO);
    } else {
        // Sin venta en curso (o cancelada): directo a "Bienvenido"
        mostrarPantalla('esperando');
    }
}

// Pide el estado actual una sola vez, por si la pantalla se abre con una venta ya empezada
async function cargarEstadoInicial() {
    try {
        const respuesta = await fetch('/api/estado');
        aplicarEstado(await respuesta.json());
    } catch (error) {
        console.log('No se pudo consultar el estado inicial', error);
    }
}

// Conecta con Reverb y escucha los cambios del carrito en vivo
function escucharCambios() {
    // Datos de conexión que Blade puso en los atributos data-reverb-* del <body>
    const configuracion = document.body.dataset;

    const conexion = new Echo({
        broadcaster: 'reverb',
        key: configuracion.reverbClave,
        wsHost: configuracion.reverbHost,
        wsPort: Number(configuracion.reverbPuerto),
        wssPort: Number(configuracion.reverbPuerto),
        forceTLS: configuracion.reverbEsquema === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    // El punto inicial indica que es el nombre definido en broadcastAs()
    conexion.channel('carrito').listen('.carrito.actualizado', aplicarEstado);
}

// Al abrir la pantalla: primero el estado actual por HTTP, luego los cambios por WebSocket
cargarEstadoInicial();
escucharCambios();
