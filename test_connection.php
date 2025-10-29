<?php
/**
 * Archivo de prueba de conexión a la base de datos
 * Acceder directamente: https://ojitodesol.com.ar/test_connection.php
 */

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Test Conexión DB</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:5px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}";
echo ".error{color:#d32f2f;background:#ffebee;padding:10px;border-left:4px solid #d32f2f;}";
echo ".success{color:#388e3c;background:#e8f5e9;padding:10px;border-left:4px solid #388e3c;}";
echo ".info{color:#1976d2;background:#e3f2fd;padding:10px;border-left:4px solid #1976d2;}";
echo "pre{background:#f5f5f5;padding:10px;border-radius:3px;overflow-x:auto;}";
echo "</style></head><body>";
echo "<h1>🔍 Test de Conexión a Base de Datos</h1>";

// Información del sistema
echo "<div class='box'>";
echo "<h2>📋 Información del Sistema</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>MySQLi Extension:</strong> " . (extension_loaded('mysqli') ? '✅ Disponible' : '❌ No disponible') . "</p>";
echo "</div>";

// Configuración a probar
echo "<div class='box'>";
echo "<h2>⚙️ Configuración de Conexión</h2>";

$host_original = getenv('DB_HOST') ?: "localhost"; // Cambiar a localhost por defecto
$user = getenv('DB_USER') ?: "c2880275_ventas";
$clave = getenv('DB_PASSWORD') ?: "wego76FIfe";
$bd = getenv('DB_NAME') ?: "c2880275_ventas";
$port = (int) (getenv('DB_PORT') ?: 3306);

echo "<pre>";
echo "Host original: " . htmlspecialchars($host_original) . "\n";
echo "Puerto: " . $port . "\n";
echo "Usuario: " . htmlspecialchars($user) . "\n";
echo "Base de datos: " . htmlspecialchars($bd) . "\n";
echo "Contraseña: " . (empty($clave) ? "❌ Vacía" : "✅ Configurada (" . strlen($clave) . " caracteres)") . "\n";
echo "</pre>";
echo "</div>";

// Configuraciones a probar en orden
$configs_to_try = array(
    array('host' => 'localhost', 'port' => null, 'socket' => null, 'desc' => 'localhost (sin puerto, usa socket automático)'),
    array('host' => 'localhost', 'port' => 3306, 'socket' => null, 'desc' => 'localhost:3306'),
    array('host' => '127.0.0.1', 'port' => 3306, 'socket' => null, 'desc' => '127.0.0.1:3306'),
);

// Intentar conexión con diferentes configuraciones
echo "<div class='box'>";
echo "<h2>🔌 Probando Diferentes Configuraciones...</h2>";

$conexion = false;
$config_exitosa = null;

// Probar cada configuración
foreach ($configs_to_try as $config) {
    echo "<div style='margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 3px;'>";
    echo "<strong>Probando:</strong> " . htmlspecialchars($config['desc']) . "<br>";
    
    try {
        if ($config['port'] === null) {
            // Sin puerto específico (usa socket automático si está disponible)
            $conexion_temp = @mysqli_connect($config['host'], $user, $clave, $bd);
        } else {
            $conexion_temp = @mysqli_connect($config['host'], $user, $clave, $bd, $config['port']);
        }
        
        if ($conexion_temp) {
            echo "<span style='color: green;'>✅ <strong>¡CONEXIÓN EXITOSA!</strong></span><br>";
            $conexion = $conexion_temp;
            $config_exitosa = $config;
            echo "</div>";
            break; // Salir del bucle si encontramos una conexión exitosa
        } else {
            $error_msg_temp = mysqli_connect_error();
            $error_code_temp = mysqli_connect_errno();
            echo "<span style='color: red;'>❌ Error: " . htmlspecialchars($error_msg_temp) . " (Código: " . $error_code_temp . ")</span>";
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>❌ Excepción: " . htmlspecialchars($e->getMessage()) . "</span>";
    }
    
    echo "</div>";
}

if (!$conexion) {
    echo "<div class='error'>";
    echo "<h3>❌ Ninguna Configuración Funcionó</h3>";
    echo "<p><strong>Recomendaciones:</strong></p>";
    echo "<ul>";
    echo "<li>Contacta con tu proveedor de hosting (Ferozo) para obtener el host correcto de MySQL</li>";
    echo "<li>Verifica que el usuario tenga permisos para conectarse desde el servidor web</li>";
    echo "<li>Revisa el panel de control de Ferozo para la información de conexión MySQL</li>";
    echo "<li>Puede ser que necesites usar un socket Unix específico o un host diferente</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h3>✅ Conexión Exitosa</h3>";
    
    // Información de la conexión
    $server_info = mysqli_get_server_info($conexion);
    $host_info = mysqli_get_host_info($conexion);
    
    echo "<p><strong>Versión del servidor MySQL:</strong> " . htmlspecialchars($server_info) . "</p>";
    echo "<p><strong>Información del host:</strong> " . htmlspecialchars($host_info) . "</p>";
    
    // Probar consulta simple
    echo "<h4>📊 Probando Consulta...</h4>";
    $query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuario WHERE estado = 1");
    
    if ($query) {
        $result = mysqli_fetch_assoc($query);
        echo "<p><strong>✅ Consulta exitosa:</strong> Se encontraron " . $result['total'] . " usuarios activos.</p>";
        
        // Listar usuarios
        $query2 = mysqli_query($conexion, "SELECT idusuario, nombre, usuario, correo FROM usuario WHERE estado = 1 LIMIT 5");
        if ($query2 && mysqli_num_rows($query2) > 0) {
            echo "<h4>👥 Usuarios Activos (primeros 5):</h4>";
            echo "<table style='width:100%;border-collapse:collapse;'>";
            echo "<tr style='background:#f0f0f0;'><th style='padding:8px;text-align:left;border:1px solid #ddd;'>ID</th><th style='padding:8px;text-align:left;border:1px solid #ddd;'>Nombre</th><th style='padding:8px;text-align:left;border:1px solid #ddd;'>Usuario</th><th style='padding:8px;text-align:left;border:1px solid #ddd;'>Correo</th></tr>";
            while ($row = mysqli_fetch_assoc($query2)) {
                echo "<tr>";
                echo "<td style='padding:8px;border:1px solid #ddd;'>" . htmlspecialchars($row['idusuario']) . "</td>";
                echo "<td style='padding:8px;border:1px solid #ddd;'>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td style='padding:8px;border:1px solid #ddd;'>" . htmlspecialchars($row['usuario']) . "</td>";
                echo "<td style='padding:8px;border:1px solid #ddd;'>" . htmlspecialchars($row['correo']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<div class='error'>";
        echo "<p><strong>❌ Error en consulta:</strong> " . htmlspecialchars(mysqli_error($conexion)) . "</p>";
        echo "</div>";
    }
    
    // Mostrar configuración exitosa
    echo "<div class='info' style='margin-top: 20px;'>";
    echo "<h3>✅ Configuración Exitosa Encontrada</h3>";
    echo "<p><strong>Host:</strong> " . htmlspecialchars($config_exitosa['host']) . "</p>";
    if ($config_exitosa['port'] !== null) {
        echo "<p><strong>Puerto:</strong> " . $config_exitosa['port'] . "</p>";
    } else {
        echo "<p><strong>Puerto:</strong> Sin especificar (usa socket automático)</p>";
    }
    echo "<p><strong>Descripción:</strong> " . htmlspecialchars($config_exitosa['desc']) . "</p>";
    echo "<p style='background: #fff3cd; padding: 10px; border-radius: 3px; margin-top: 10px;'>";
    echo "<strong>⚠️ Importante:</strong> Actualiza <code>conexion.php</code> con esta configuración:<br>";
    echo "<code>\$host = '" . htmlspecialchars($config_exitosa['host']) . "';</code><br>";
    if ($config_exitosa['port'] === null) {
        echo "<code>// No especificar puerto o usar null para socket automático</code>";
    } else {
        echo "<code>\$port = " . $config_exitosa['port'] . ";</code>";
    }
    echo "</p>";
    echo "</div>";
    
    // Cerrar conexión
    mysqli_close($conexion);
    echo "</div>";
}

echo "<div class='box'>";
echo "<h2>💡 Siguientes Pasos</h2>";
echo "<ul>";
echo "<li>Si la conexión fue exitosa, el problema está en otro lugar del código.</li>";
echo "<li>Si falló, revisa los datos de conexión mostrados arriba.</li>";
echo "<li>Verifica que el usuario tenga permisos sobre la base de datos.</li>";
echo "<li>Confirma que el servidor MySQL esté corriendo.</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>

