<?php
/**
 * Script de prueba para verificar la respuesta JSON del instalador
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Instalador - JSON Response</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #00ff00;
        }
        .section {
            background: #2d2d2d;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #00ff00;
        }
        .error { border-left-color: #ff0000; color: #ff0000; }
        .warning { border-left-color: #ffa500; color: #ffa500; }
        pre {
            background: #000;
            padding: 10px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        h2 { color: #00ffff; }
    </style>
</head>
<body>
    <h1>🔍 Test de Respuesta JSON del Instalador</h1>
    
    <?php
    // Hacer petición al instalador
    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/setup_facturacion_auto.php';
    
    echo "<div class='section'>";
    echo "<h2>📍 URL del Instalador:</h2>";
    echo "<pre>$url</pre>";
    echo "</div>";
    
    // Iniciar cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    
    if (curl_errno($ch)) {
        echo "<div class='section error'>";
        echo "<h2>❌ Error de cURL:</h2>";
        echo "<pre>" . curl_error($ch) . "</pre>";
        echo "</div>";
    }
    
    curl_close($ch);
    
    echo "<div class='section'>";
    echo "<h2>📊 Información de Respuesta:</h2>";
    echo "<pre>";
    echo "HTTP Code: $http_code\n";
    echo "Content-Type: $content_type\n";
    echo "Response Length: " . strlen($response) . " bytes\n";
    echo "</pre>";
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>📄 Respuesta Cruda (Raw):</h2>";
    echo "<pre>" . htmlspecialchars(substr($response, 0, 2000)) . "</pre>";
    if (strlen($response) > 2000) {
        echo "<p>... (respuesta truncada, total: " . strlen($response) . " bytes)</p>";
    }
    echo "</div>";
    
    // Intentar decodificar JSON
    $json = json_decode($response, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<div class='section'>";
        echo "<h2>✅ JSON Decodificado Exitosamente:</h2>";
        echo "<pre>";
        echo "Success: " . ($json['success'] ? 'true' : 'false') . "\n";
        echo "Message: " . ($json['message'] ?? 'N/A') . "\n";
        echo "Log entries: " . (isset($json['log']) ? count($json['log']) : 0) . "\n";
        echo "Warnings: " . (isset($json['warnings']) ? count($json['warnings']) : 0) . "\n";
        echo "Errors: " . (isset($json['errors']) ? count($json['errors']) : 0) . "\n";
        echo "</pre>";
        
        if (isset($json['log']) && !empty($json['log'])) {
            echo "<h3>📝 Log (primeras 20 líneas):</h3>";
            echo "<pre>";
            foreach (array_slice($json['log'], 0, 20) as $line) {
                echo htmlspecialchars($line) . "\n";
            }
            echo "</pre>";
        } else {
            echo "<div class='warning'><strong>⚠️ ADVERTENCIA: El array 'log' está vacío!</strong></div>";
        }
        
        if (isset($json['errors']) && !empty($json['errors'])) {
            echo "<h3 style='color: #ff0000;'>❌ Errores:</h3>";
            echo "<pre style='color: #ff0000;'>";
            foreach ($json['errors'] as $error) {
                echo htmlspecialchars($error) . "\n";
            }
            echo "</pre>";
        }
        
        if (isset($json['warnings']) && !empty($json['warnings'])) {
            echo "<h3 style='color: #ffa500;'>⚠️ Advertencias:</h3>";
            echo "<pre style='color: #ffa500;'>";
            foreach ($json['warnings'] as $warning) {
                echo htmlspecialchars($warning) . "\n";
            }
            echo "</pre>";
        }
        
        echo "<h3>🔍 JSON Completo:</h3>";
        echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='section error'>";
        echo "<h2>❌ Error al Decodificar JSON:</h2>";
        echo "<pre>";
        echo "Error: " . json_last_error_msg() . "\n";
        echo "Error Code: " . json_last_error() . "\n";
        echo "</pre>";
        echo "</div>";
    }
    ?>
    
    <div class='section'>
        <h2>🔄 Acciones:</h2>
        <button onclick="location.reload();" style="padding: 10px 20px; background: #00ff00; color: #000; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
            🔄 Ejecutar Nuevamente
        </button>
        <a href="configuracion_sistema.php" style="margin-left: 10px; padding: 10px 20px; background: #0080ff; color: #fff; text-decoration: none; border-radius: 5px; display: inline-block;">
            🏠 Volver a Configuración
        </a>
    </div>
    
    <div class='section warning'>
        <h2>💡 Información:</h2>
        <p>Este script hace una petición POST al instalador y muestra la respuesta completa.</p>
        <p>Si el log está vacío, puede ser porque:</p>
        <ul>
            <li>Hay un error de PHP que interrumpe la ejecución antes de generar el log</li>
            <li>La sesión no está iniciada correctamente</li>
            <li>Hay problemas con la conexión a la base de datos</li>
            <li>El archivo SQL no se encuentra</li>
        </ul>
    </div>
</body>
</html>

