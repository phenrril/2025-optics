<?php
    // Configuración de base de datos
    $host = getenv('DB_HOST') ?: "127.0.0.1";
    $user = getenv('DB_USER') ?: "u375391241_opticaojito";
    $clave = getenv('DB_PASSWORD') ?: "Optica2024";
    $bd = getenv('DB_NAME') ?: "u375391241_sis_ventas";
    
    // En Docker no usamos puerto, MySQL está en el puerto por defecto
    $port = getenv('DB_PORT') ?: "3306";
    $host_with_port = $host . ':' . $port;
    
    $conexion = mysqli_connect($host_with_port, $user, $clave, $bd);
    if (mysqli_connect_errno()){
        echo "No se pudo conectar a la base de datos: " . mysqli_connect_error();
        exit();
    }
    mysqli_select_db($conexion, $bd) or die("No se encuentra la base de datos");
    mysqli_set_charset($conexion, "utf8");
?>
