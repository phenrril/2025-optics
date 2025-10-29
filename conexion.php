<?php
    // Configuración de base de datos
    $host = getenv('DB_HOST') ?: "127.0.0.1"; // usar 127.0.0.1 evita problemas de socket con "localhost"
    $user = getenv('DB_USER') ?: "c2880275_ventas";
    $clave = getenv('DB_PASSWORD') ?: "wego76FIfe";
    $bd = getenv('DB_NAME') ?: "c2880275_ventas";

    // Puerto por defecto 3306 si no se define
    $port = (int) (getenv('DB_PORT') ?: 3306);

    // Conexión mysqli pasando el puerto como argumento separado
    $conexion = mysqli_connect($host, $user, $clave, $bd, $port);
    if (!$conexion) {
        http_response_code(500);
        echo "No se pudo conectar a la base de datos: " . mysqli_connect_error();
        exit();
    }

    // Establecer charset
    if (!mysqli_set_charset($conexion, "utf8")) {
        // Si falla, no abortamos toda la app, pero dejamos constancia
        error_log("No se pudo establecer el charset UTF-8: " . mysqli_error($conexion));
    }
?>
