<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente de Sockets PHP</title>
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
        .client-info {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #e67e22;
        }
        .message-container {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #feefe3; /* Color naranja claro */
            border-left: 4px solid #e67e22;
        }
        .response-container {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #ebf7ee; /* Color verde claro */
            border-left: 4px solid #2ecc71;
        }
        .error-container {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #fee; /* Color rojo claro */
            border-left: 4px solid #e74c3c;
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
        .badge-error {
            background-color: #e74c3c;
        }
        .server-address {
            color: #7f8c8d;
            font-weight: bold;
        }
        .message-content {
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #eee;
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
        }
        .action-button:hover {
            background-color: #2980b9;
        }
        .button-container {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Cliente de Sockets PHP</h1>
            <div>
                <span class="badge badge-success" id="status-badge">Conectando</span>
            </div>
        </header>

        <div id="result-container">
            <?php
            
            $servidor = "25.37.107.227";
            $port = 25009;
            try {
                
                $nombre_cliente = get_current_user();
                $ip_cliente = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
                $nombre_en_red = gethostname();
                $sistema_operativo_cliente = php_uname();
                $servicios_php = implode(', ', get_loaded_extensions());
                $fecha_hora_cliente = date('Y-m-d H:i:s');
                
           
                echo '<div class="client-info">';
                echo '<h2>Información del Cliente</h2>';
                echo '<p><strong>Usuario:</strong> ' . htmlspecialchars($nombre_cliente) . '</p>';
                echo '<p><strong>IP:</strong> ' . htmlspecialchars($ip_cliente) . '</p>';
                echo '<p><strong>Nombre en red:</strong> ' . htmlspecialchars($nombre_en_red) . '</p>';
                echo '<p><strong>Fecha y hora:</strong> ' . htmlspecialchars($fecha_hora_cliente) . '</p>';
                echo '<p><strong>Sistema operativo:</strong> ' . htmlspecialchars($sistema_operativo_cliente) . '</p>';
                echo '</div>';
                
              
                $mensaje = "Conectando*****************
<br><br>Soy el cliente = $nombre_cliente
<br><br>Como cliente tengo la IP: $ip_cliente
<br><br>Como cliente tengo el nombre en la red de: $nombre_en_red
<br><br>La fecha y hora que obtengo como cliente es: $fecha_hora_cliente
<br><br>Como cliente tengo el sistema operativo: $sistema_operativo_cliente
<br><br>Como cliente tengo los siguientes servicios de PHP activos: $servicios_php
<br><br>";
                
             
                echo '<div class="message-container">';
                echo '<h2>Mensaje enviado al servidor</h2>';
                echo '<p>Destino: <span class="server-address">' . htmlspecialchars($servidor) . ':' . htmlspecialchars($port) . '</span></p>';
                echo '<div class="message-content">' . $mensaje . '</div>';
                echo '</div>';
                
                
                if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
                    throw new Exception("Error al crear el socket: " . socket_strerror(socket_last_error()));
                }
                
                
                socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 5, 'usec' => 0));
                socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 5, 'usec' => 0));
                
                
                if (socket_connect($socket, $servidor, $port) === false) {
                    throw new Exception("Error al conectar al servidor: " . socket_strerror(socket_last_error($socket)));
                }
                
                
                if (socket_write($socket, $mensaje, strlen($mensaje)) === false) {
                    throw new Exception("Error al enviar datos: " . socket_strerror(socket_last_error($socket)));
                }
                
                
                $buffer = '';
                if (($result = socket_read($socket, 4096)) === false) {
                    throw new Exception("Error al leer respuesta: " . socket_strerror(socket_last_error($socket)));
                }
                
                
                echo '<div class="response-container">';
                echo '<h2>Respuesta del servidor</h2>';
                echo '<div class="message-content">' . $result . '</div>';
                echo '</div>';
                
              
                echo '<script>document.getElementById("status-badge").innerText = "Conectado";</script>';
                
                socket_close($socket);
                
            } catch (Exception $e) {
              
                echo '<div class="error-container">';
                echo '<h2>Error de conexión</h2>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
                
                
                echo '<script>
                    var badge = document.getElementById("status-badge");
                    badge.innerText = "Error";
                    badge.className = "badge badge-error";
                </script>';
                
           
                if (isset($socket) && $socket !== false) {
                    socket_close($socket);
                }
            }
            ?>
        </div>

        <div class="button-container">
            <button class="action-button" onclick="window.location.reload()">Reconectar</button>
        </div>
    </div>
</body>
</html>