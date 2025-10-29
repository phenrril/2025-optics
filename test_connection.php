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

$host = getenv('DB_HOST') ?: "127.0.0.1";
$user = getenv('DB_USER') ?: "c2880275_ventas";
$clave = getenv('DB_PASSWORD') ?: "wego76FIfe";
$bd = getenv('DB_NAME') ?: "c2880275_ventas";
$port = (int) (getenv('DB_PORT') ?: 3306);

echo "<pre>";
echo "Host: " . htmlspecialchars($host) . "\n";
echo "Puerto: " . $port . "\n";
echo "Usuario: " . htmlspecialchars($user) . "\n";
echo "Base de datos: " . htmlspecialchars($bd) . "\n";
echo "Contraseña: " . (empty($clave) ? "❌ Vacía" : "✅ Configurada (" . strlen($clave) . " caracteres)") . "\n";
echo "</pre>";
echo "</div>";

// Intentar conexión
echo "<div class='box'>";
echo "<h2>🔌 Intentando Conexión...</h2>";

$conexion = @mysqli_connect($host, $user, $clave, $bd, $port);

if (!$conexion) {
    $error_msg = mysqli_connect_error();
    $error_code = mysqli_connect_errno();
    
    echo "<div class='error'>";
    echo "<h3>❌ Error de Conexión</h3>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($error_msg) . "</p>";
    echo "<p><strong>Código de error:</strong> " . $error_code . "</p>";
    
    // Información adicional según el código de error
    switch ($error_code) {
        case 1045:
            echo "<p><strong>Problema:</strong> Acceso denegado. Verifica usuario y contraseña.</p>";
            break;
        case 1049:
            echo "<p><strong>Problema:</strong> Base de datos no existe.</p>";
            break;
        case 2002:
            echo "<p><strong>Problema:</strong> No se puede conectar al servidor MySQL.</p>";
            echo "<p><strong>Sugerencia:</strong> Verifica que el host sea correcto. Prueba con 'localhost' en lugar de '127.0.0.1' o viceversa.</p>";
            break;
        case 2006:
            echo "<p><strong>Problema:</strong> El servidor MySQL se ha ido.</p>";
            break;
        default:
            echo "<p><strong>Sugerencia:</strong> Verifica la configuración de conexión.</p>";
    }
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h3>✅ Conexión Exitosa</h3>";
    
    // Información de la conexión
    $server_info = mysqli_get_server_info($conexion);
    $host_info = mysqli_get_host_info($conexion);
    $protocol_version = mysqli_get_protocol_info($conexion);
    
    echo "<p><strong>Versión del servidor MySQL:</strong> " . htmlspecialchars($server_info) . "</p>";
    echo "<p><strong>Información del host:</strong> " . htmlspecialchars($host_info) . "</p>";
    echo "<p><strong>Versión del protocolo:</strong> " . $protocol_version . "</p>";
    
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
    
    // Cerrar conexión
    mysqli_close($conexion);
    echo "</div>";
}

// Probando con localhost si 127.0.0.1 falló
if (!$conexion && $host === "127.0.0.1") {
    echo "<div class='info'>";
    echo "<h4>🔄 Probando con 'localhost'...</h4>";
    $conexion2 = @mysqli_connect("localhost", $user, $clave, $bd, $port);
    
    if ($conexion2) {
        echo "<p class='success'>✅ ¡Conexión exitosa con 'localhost'!</p>";
        echo "<p><strong>Recomendación:</strong> Cambia el host en conexion.php de '127.0.0.1' a 'localhost'</p>";
        mysqli_close($conexion2);
    } else {
        echo "<p>❌ También falló con 'localhost': " . htmlspecialchars(mysqli_connect_error()) . "</p>";
    }
    echo "</div>";
}

echo "</div>";

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

