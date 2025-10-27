<?php
/**
 * Script para ocultar automáticamente todos los productos sin stock en la base de datos
 * Este script puede ejecutarse una vez para actualizar productos existentes
 */

// Configuración de base de datos
$host = getenv('DB_HOST') ?: "127.0.0.1";
$user = getenv('DB_USER') ?: "u375391241_opticaojito";
$clave = getenv('DB_PASSWORD') ?: "Optica2024";
$bd = getenv('DB_NAME') ?: "u375391241_sis_ventas";
$port = getenv('DB_PORT') ?: "3306";
$host_with_port = $host . ':' . $port;

$conexion = mysqli_connect($host_with_port, $user, $clave, $bd);
if (mysqli_connect_errno()) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error() . "\n");
}
mysqli_select_db($conexion, $bd) or die("Error: No se encuentra la base de datos\n");
mysqli_set_charset($conexion, "utf8");

// Detectar si se llama desde web (AJAX) o desde terminal
$is_web = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST) || php_sapi_name() !== 'cli';

// Ocultar todos los productos que tienen existencia = 0
$query = mysqli_query($conexion, "UPDATE producto SET estado = 0 WHERE existencia = 0 AND estado = 1");

if ($query) {
    $filas_afectadas = mysqli_affected_rows($conexion);
    
    // Obtener estadísticas
    $stats_query = mysqli_query($conexion, "SELECT 
        COUNT(*) as total_productos,
        SUM(CASE WHEN existencia > 0 THEN 1 ELSE 0 END) as con_stock,
        SUM(CASE WHEN existencia = 0 THEN 1 ELSE 0 END) as sin_stock
    FROM producto");
    
    $stats = mysqli_fetch_assoc($stats_query);
    
    if ($is_web) {
        // Respuesta para web (JSON)
        $response = [
            'success' => true,
            'message' => "Se ocultaron $filas_afectadas productos sin stock.",
            'html' => "
                <div class='alert alert-success'>
                    <h5><i class='fas fa-check-circle'></i> Proceso completado exitosamente</h5>
                    <hr>
                    <p><strong>Productos ocultados:</strong> $filas_afectadas</p>
                    <p><strong>Total de productos:</strong> {$stats['total_productos']}</p>
                    <p><strong>Productos con stock:</strong> {$stats['con_stock']}</p>
                    <p><strong>Productos sin stock (ocultos):</strong> {$stats['sin_stock']}</p>
                </div>
            ",
            'stats' => $stats
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Respuesta para terminal
        echo "=== Ocultando Productos Sin Stock ===\n\n";
        echo "✓ Se ocultaron $filas_afectadas productos sin stock.\n";
        echo "\n=== Estadísticas Actuales ===\n";
        echo "Total de productos: {$stats['total_productos']}\n";
        echo "Productos con stock: {$stats['con_stock']}\n";
        echo "Productos sin stock (ocultos): {$stats['sin_stock']}\n";
        echo "\n=== Proceso Completado ===\n";
    }
    
} else {
    if ($is_web) {
        $response = [
            'success' => false,
            'message' => 'Error al ocultar productos',
            'html' => "<div class='alert alert-danger'>Error al ocultar productos: " . mysqli_error($conexion) . "</div>"
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo "✗ Error al ocultar productos: " . mysqli_error($conexion) . "\n";
    }
}

mysqli_close($conexion);

