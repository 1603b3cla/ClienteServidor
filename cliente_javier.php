<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = "25.36.93.208"; // Cambia a la IP real del servidor si es necesario
$port = 9090; // Debe coincidir con el del servidor

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (!$socket) die("❌ Error al crear el socket: " . socket_strerror(socket_last_error()) . "\n");

if (!socket_connect($socket, $server, $port)) {
    die("❌ Error al conectar con el servidor: " . socket_strerror(socket_last_error()) . "\n");
}

// Obtener información del cliente
$nombre_usuario = get_current_user();
$ip_cliente = gethostbyname(gethostname());
$sistema_cliente = PHP_OS;
$fecha_hora_cliente = date('l jS \of F Y h:i:s A');

$datos = "Soy el cliente= $nombre_usuario\n" .
         "Como cliente tengo la IP: $ip_cliente\n" .
         "La fecha y hora que obtengo como cliente es: $fecha_hora_cliente\n" .
         "Como cliente tengo el sistema operativo: $sistema_cliente\n";

// Enviar datos al servidor
if (!socket_write($socket, $datos, strlen($datos))) {
    die("❌ Error al enviar datos: " . socket_strerror(socket_last_error()) . "\n");
}

// Leer respuesta del servidor
$respuesta = socket_read($socket, 2048);
if (!$respuesta) {
    die("❌ Error al leer la respuesta: " . socket_strerror(socket_last_error()) . "\n");
}

echo "🔹 **Información recibida desde el servidor:**\n$respuesta\n";

// Cerrar el socket
socket_shutdown($socket);
socket_close($socket);
?>