<?php
    // Habilitar reporte de errores para depuración (en producción debería ser 0)
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // No mostrar en pantalla, solo capturar
    ini_set('log_errors', 1);

    // Configuración de base de datos
    $host = getenv('DB_HOST') ?: "127.0.0.1"; // usar 127.0.0.1 evita problemas de socket con "localhost"
    $user = getenv('DB_USER') ?: "c2880275_ventas";
    $clave = getenv('DB_PASSWORD') ?: "wego76FIfe";
    $bd = getenv('DB_NAME') ?: "c2880275_ventas";

    // Puerto por defecto 3306 si no se define
    $port = (int) (getenv('DB_PORT') ?: 3306);

    // Inicializar variable de conexión
    $conexion = false;
    $error_conexion = null;

    // Intentar conexión mysqli pasando el puerto como argumento separado
    try {
        $conexion = @mysqli_connect($host, $user, $clave, $bd, $port);
        
        if (!$conexion) {
            $error_conexion = mysqli_connect_error();
            $error_code = mysqli_connect_errno();
            
            // Guardar error para que pueda ser capturado por el script que incluye este archivo
            if (!isset($GLOBALS['db_connection_error'])) {
                $GLOBALS['db_connection_error'] = array(
                    'message' => $error_conexion,
                    'code' => $error_code,
                    'host' => $host,
                    'port' => $port,
                    'database' => $bd,
                    'user' => $user
                );
            }
        } else {
            // Establecer charset solo si la conexión fue exitosa
            if (!mysqli_set_charset($conexion, "utf8")) {
                // Si falla, no abortamos toda la app, pero dejamos constancia
                error_log("No se pudo establecer el charset UTF-8: " . mysqli_error($conexion));
            }
        }
    } catch (Exception $e) {
        $error_conexion = $e->getMessage();
        if (!isset($GLOBALS['db_connection_error'])) {
            $GLOBALS['db_connection_error'] = array(
                'message' => $error_conexion,
                'code' => 'EXCEPTION',
                'exception' => $e->getMessage()
            );
        }
    }
?>
