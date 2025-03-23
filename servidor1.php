<?php

$servidor = "25.36.58.229";
$port = 25004;

set_time_limit(0);

$log_file = "socket_log.txt";
file_put_contents($log_file, "=== INICIO DEL SERVIDOR: " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

ob_start();
ob_implicit_flush(true);

// Función para obtener la IP pública del servidor
function getPublicIP() {
    $ip = file_get_contents('https://api.ipify.org');
    return $ip !== false ? $ip : 'No disponible';
}

// Función para obtener la ubicación geográfica de una IP
function getIPLocation($ip) {
    $url = "http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon,timezone,isp";
    $response = @file_get_contents($url);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data && $data['status'] === 'success') {
            return $data;
        }
    }
    
    return [
        'status' => 'fail',
        'country' => 'Desconocido',
        'regionName' => 'Desconocido',
        'city' => 'Desconocido',
        'lat' => 0,
        'lon' => 0,
        'timezone' => 'Desconocido',
        'isp' => 'Desconocido'
    ];
}

// Obtener la IP pública del servidor
$public_ip = getPublicIP();

// Obtener la IP privada del servidor (local)
$private_ip = gethostbyname(gethostname());

// Obtener la ubicación del servidor
$server_location = getIPLocation($public_ip);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidor de Sockets PHP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .server-info {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .location-info {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #9b59b6;
        }
        .map-container {
            height: 250px;
            margin-bottom: 20px;
            border-radius: 6px;
            overflow: hidden;
        }
        .connection-info {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #f9f9f9;
            border-left: 4px solid #2ecc71;
        }
        .client-data {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .timestamp {
            color: #7f8c8d;
            font-size: 14px;
        }
        .separator {
            height: 1px;
            background-color: #eee;
            margin: 20px 0;
        }
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        .stat-box h3 {
            margin-top: 0;
            color: #495057;
        }
        .stat-box p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #3498db;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: Consolas, Monaco, 'Andale Mono', monospace;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            border-radius: 10px;
            background-color: #3498db;
            color: white;
        }
        .badge-success {
            background-color: #2ecc71;
        }
        .badge-warning {
            background-color: #f39c12;
        }
        .badge-danger {
            background-color: #e74c3c;
        }
        .action-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        .action-button:hover {
            background-color: #2980b9;
        }
        .button-container {
            margin: 20px 0;
            text-align: right;
        }
        .ip-info {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }
        .ip-box {
            flex: 1;
            padding: 10px;
            background-color: #f0f8ff;
            border-radius: 4px;
            border: 1px solid #b3d8ff;
        }
        .ip-box h4 {
            margin-top: 0;
            color: #4a73ab;
        }
        .ip-box p {
            margin-bottom: 0;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Servidor de Sockets PHP</h1>
            <span class="badge badge-success">En línea</span>
        </header>

        <div class="server-info">
            <h2>Información del Servidor</h2>
            <div class="ip-info">
                <div class="ip-box">
                    <h4>IP Pública</h4>
                    <p><?php echo $public_ip; ?></p>
                </div>
                <div class="ip-box">
                    <h4>IP Privada</h4>
                    <p><?php echo $private_ip; ?></p>
                </div>
            </div>
            <p><strong>Dirección socket:</strong> <?php echo $servidor; ?>:<?php echo $port; ?></p>
            <p><strong>Iniciado:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Hostname:</strong> <?php echo gethostname(); ?></p>
            <p><strong>Sistema Operativo:</strong> <?php echo php_uname(); ?></p>
        </div>

        <div class="location-info">
            <h2>Ubicación del Servidor</h2>
            <p><strong>País:</strong> <?php echo $server_location['country']; ?></p>
            <p><strong>Región:</strong> <?php echo $server_location['regionName']; ?></p>
            <p><strong>Ciudad:</strong> <?php echo $server_location['city']; ?></p>
            <p><strong>ISP:</strong> <?php echo $server_location['isp']; ?></p>
            <p><strong>Zona horaria:</strong> <?php echo $server_location['timezone']; ?></p>
            <p><strong>Coordenadas:</strong> <?php echo $server_location['lat']; ?>, <?php echo $server_location['lon']; ?></p>
        </div>

        <?php if ($server_location['lat'] != 0 && $server_location['lon'] != 0): ?>
        <div class="map-container">
            <iframe
                width="100%"
                height="100%"
                frameborder="0"
                scrolling="no"
                marginheight="0"
                marginwidth="0"
                src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo $server_location['lon']-0.02; ?>%2C<?php echo $server_location['lat']-0.02; ?>%2C<?php echo $server_location['lon']+0.02; ?>%2C<?php echo $server_location['lat']+0.02; ?>&amp;layer=mapnik&amp;marker=<?php echo $server_location['lat']; ?>%2C<?php echo $server_location['lon']; ?>"
                style="border: 1px solid #ccc">
            </iframe>
        </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-box">
                <h3>Conexiones</h3>
                <p id="connection-count">0</p>
            </div>
            <div class="stat-box">
                <h3>Mensajes Recibidos</h3>
                <p id="message-count">0</p>
            </div>
            <div class="stat-box">
                <h3>Tiempo Activo</h3>
                <p id="uptime">0m</p>
            </div>
        </div>

        <div class="button-container">
            <button class="action-button" onclick="window.location.reload()">Actualizar</button>
        </div>

        <h2>Registro de Conexiones</h2>
        <div id="connections-log">
<?php

$connectionCount = 0;
$messageCount = 0;
$startTime = time();

if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
    echo "<div class='client-data badge-danger'>";
    echo "Error al crear socket: " . socket_strerror(socket_last_error()) . "<br>";
    echo "</div>";
    exit;
}

socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

if (socket_bind($socket, $servidor, $port) === false) {
    echo "<div class='client-data badge-danger'>";
    echo "Error al vincular socket: " . socket_strerror(socket_last_error($socket)) . "<br>";
    echo "</div>";
    exit;
}

if (socket_listen($socket, 5) === false) {
    echo "<div class='client-data badge-danger'>";
    echo "Error al escuchar: " . socket_strerror(socket_last_error($socket)) . "<br>";
    echo "</div>";
    exit;
}

echo "<div class='connection-info'>";
echo "<span class='timestamp'>[" . date('Y-m-d H:i:s') . "]</span> ";
echo "Servidor iniciado en $servidor:$port - Esperando conexiones...";
echo "</div>";

socket_set_nonblock($socket);

$endTime = time() + 60;
while (time() < $endTime) {
    $cliente = @socket_accept($socket);
    if ($cliente !== false) {
        $connectionCount++;
        
        socket_getpeername($cliente, $cliente_ip);
        
        // Obtener la ubicación del cliente
        $client_location = getIPLocation($cliente_ip);
        
        $mensaje_conexion = "[" . date('Y-m-d H:i:s') . "] Nueva conexión desde: $cliente_ip";
        file_put_contents($log_file, $mensaje_conexion . "\n", FILE_APPEND);
        
        echo "<div class='connection-info'>";
        echo "<span class='timestamp'>[" . date('Y-m-d H:i:s') . "]</span> ";
        echo "Nueva conexión desde: <strong>$cliente_ip</strong>";
        echo "</div>";
        
        // Mostrar la información de ubicación del cliente
        echo "<div class='location-info'>";
        echo "<h3>Ubicación del Cliente</h3>";
        echo "<p><strong>IP:</strong> $cliente_ip</p>";
        echo "<p><strong>País:</strong> " . $client_location['country'] . "</p>";
        echo "<p><strong>Región:</strong> " . $client_location['regionName'] . "</p>";
        echo "<p><strong>Ciudad:</strong> " . $client_location['city'] . "</p>";
        echo "<p><strong>ISP:</strong> " . $client_location['isp'] . "</p>";
        echo "</div>";
        
        // Si tenemos coordenadas, mostrar un mapa pequeño
        if ($client_location['lat'] != 0 && $client_location['lon'] != 0) {
            echo "<div class='map-container'>";
            echo "<iframe
                width='100%'
                height='100%'
                frameborder='0'
                scrolling='no'
                marginheight='0'
                marginwidth='0'
                src='https://www.openstreetmap.org/export/embed.html?bbox=" . ($client_location['lon']-0.02) . "%2C" . ($client_location['lat']-0.02) . "%2C" . ($client_location['lon']+0.02) . "%2C" . ($client_location['lat']+0.02) . "&amp;layer=mapnik&amp;marker=" . $client_location['lat'] . "%2C" . $client_location['lon'] . "'
                style='border: 1px solid #ccc'>
            </iframe>";
            echo "</div>";
        }
        
        $entrada = socket_read($cliente, 4096);
        if ($entrada !== false) {
            $messageCount++;
            
            echo "<div class='client-data'>";
            echo "<h3>Datos recibidos de $cliente_ip:</h3>";
            echo "<pre>" . htmlspecialchars($entrada) . "</pre>";
            echo "</div>";
            
            file_put_contents($log_file, "DATOS RECIBIDOS:\n$entrada\n", FILE_APPEND);
            
            // Añadir información de ubicación a la respuesta
            $respuesta = "Conectando***************** <br><br>Soy el servidor = $servidor <br><br>Como servidor tengo el nombre en la red: " . gethostname() . 
                         " <br><br>IP Pública: " . $public_ip . 
                         " <br><br>IP Privada: " . $private_ip . 
                         " <br><br>Ubicación: " . $server_location['city'] . ", " . $server_location['regionName'] . ", " . $server_location['country'] . 
                         " <br><br>A través del socket recibí los datos de cliente que son********************** <br><br>" . $entrada . 
                         " <br><br>La fecha y hora que obtengo como servidor es: " . date('Y-m-d H:i:s') . 
                         " <br><br>Como servidor tengo el sistema operativo: " . php_uname();
            
            socket_write($cliente, $respuesta, strlen($respuesta));
            
            file_put_contents($log_file, "RESPUESTA ENVIADA:\n$respuesta\n", FILE_APPEND);
        }
        
        socket_close($cliente);
        
        echo "<script>
            document.getElementById('connection-count').textContent = '$connectionCount';
            document.getElementById('message-count').textContent = '$messageCount';
            
            // Calcular tiempo activo
            var startTime = " . $startTime . ";
            var currentTime = " . time() . ";
            var uptime = currentTime - startTime;
            var uptimeStr = Math.floor(uptime / 60) + 'm ' + (uptime % 60) + 's';
            document.getElementById('uptime').textContent = uptimeStr;
        </script>";
        
        flush();
    }
    
    usleep(100000); 
}

socket_close($socket);
?>
        </div>
        
        <div class="separator"></div>
        
        <script>
            function updateUptime() {
                var startTime = <?php echo $startTime; ?>;
                var currentTime = Math.floor(Date.now() / 1000);
                var uptime = currentTime - startTime;
                var minutes = Math.floor(uptime / 60);
                var seconds = uptime % 60;
                var uptimeStr = minutes + 'm ' + seconds + 's';
                document.getElementById('uptime').textContent = uptimeStr;
                setTimeout(updateUptime, 1000);
            }
            
            updateUptime();
        </script>
    </div>
</body>
</html>