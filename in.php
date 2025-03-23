<?php
if (function_exists('socket_create')) {
    echo "La extensión sockets está habilitada";
} else {
    echo "La extensión sockets NO está habilitada";
}
?>