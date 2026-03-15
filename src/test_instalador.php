<?php
/**
 * Test de diagnóstico para el instalador
 */
session_start();
require_once "../conexion.php";

// Solo admin
if (!isset($_SESSION['idUser']) || $_SESSION['idUser'] != 1) {
    die('Solo el administrador puede ver este test');
}

echo "<!DOCTYPE html><html><head><title>Test Instalador</title></head><body>";
echo "<h1>Diagnóstico del Instalador</h1>";

// Test 1: jQuery
echo "<h2>1. jQuery</h2>";
echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";
echo "<script>
if (typeof jQuery !== 'undefined') {
    document.write('✅ jQuery cargado: ' + jQuery.fn.jquery);
} else {
    document.write('❌ jQuery NO cargado');
}
</script><br>";

// Test 2: SweetAlert2
echo "<h2>2. SweetAlert2</h2>";
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
echo "<script>
if (typeof Swal !== 'undefined') {
    document.write('✅ SweetAlert2 cargado');
} else {
    document.write('❌ SweetAlert2 NO cargado');
}
</script><br>";

// Test 3: Estado de instalación
echo "<h2>3. Estado de Instalación</h2>";
$facturacion_instalada = file_exists(__DIR__ . '/../.facturacion_installed');
echo "Archivo .facturacion_installed: " . ($facturacion_instalada ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";

$tabla_existe = mysqli_query($conexion, "SHOW TABLES LIKE 'facturacion_config'");
echo "Tabla facturacion_config: " . (($tabla_existe && mysqli_num_rows($tabla_existe) > 0) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";

// Test 4: Archivo setup
echo "<h2>4. Archivo de Setup</h2>";
$setup_file = __DIR__ . '/setup_facturacion_auto.php';
echo "Archivo setup_facturacion_auto.php: " . (file_exists($setup_file) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";

// Test 5: Botón de prueba
echo "<h2>5. Test del Botón</h2>";
echo "<button id='testBtn' style='padding: 10px 20px; font-size: 16px; cursor: pointer;'>Click Aquí para Probar</button>";
echo "<div id='resultado' style='margin-top: 20px; padding: 10px; background: #f0f0f0;'></div>";

echo "<script>
$(document).ready(function() {
    console.log('✅ Document ready ejecutado');
    
    $('#testBtn').click(function() {
        console.log('✅ Click detectado');
        $('#resultado').html('✅ El botón funciona correctamente!');
        
        // Probar SweetAlert
        Swal.fire({
            title: 'Test Exitoso',
            text: 'El botón y SweetAlert funcionan correctamente',
            icon: 'success'
        });
    });
    
    // Verificar si el botón de instalación existe
    if ($('#btnInstalarFacturacion').length > 0) {
        console.log('✅ Botón #btnInstalarFacturacion encontrado');
        $('#resultado').append('<br>✅ Botón de instalación encontrado en la página');
    } else {
        console.log('❌ Botón #btnInstalarFacturacion NO encontrado');
        $('#resultado').append('<br>❌ Botón de instalación NO encontrado (puede estar oculto porque ya está instalado)');
    }
});
</script>";

echo "</body></html>";
?>

