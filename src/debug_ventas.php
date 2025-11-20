<?php
session_start();
include "../conexion.php";

// Solo permitir acceso si es usuario admin (id = 1) o en desarrollo
$id_user = isset($_SESSION['idUser']) ? (int)$_SESSION['idUser'] : 0;

if ($id_user != 1 && $id_user != 0) {
    die("Acceso denegado. Solo para administradores.");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #667eea; color: white; }
        .alert { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
        .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; }
        .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Ventas</h1>
    
    <?php
    // Información de sesión
    echo "<div class='alert alert-info'>";
    echo "<h3>Información de Sesión:</h3>";
    echo "<p><strong>Usuario ID:</strong> " . ($id_user ?: "No logueado") . "</p>";
    echo "<p><strong>Nombre:</strong> " . (isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "N/A") . "</p>";
    echo "<p><strong>Usuario:</strong> " . (isset($_SESSION['user']) ? $_SESSION['user'] : "N/A") . "</p>";
    echo "</div>";
    
    if (!$conexion || !is_object($conexion)) {
        echo "<div class='alert alert-danger'>❌ Error: No hay conexión a la base de datos</div>";
        exit();
    }
    
    // 1. Total de ventas en la base de datos
    $query_total = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ventas");
    $total_data = mysqli_fetch_assoc($query_total);
    $total_ventas = $total_data['total'];
    
    echo "<div class='alert alert-info'>";
    echo "<h3>📊 Estadísticas Generales:</h3>";
    echo "<p><strong>Total de ventas en BD:</strong> $total_ventas</p>";
    echo "</div>";
    
    // 2. Últimas 10 ventas (sin filtro de usuario)
    echo "<h2>Últimas 10 Ventas (Todas):</h2>";
    $query_recientes = mysqli_query($conexion, "SELECT v.id, v.id_usuario, v.id_cliente, v.total, v.fecha, c.nombre as cliente_nombre FROM ventas v LEFT JOIN cliente c ON v.id_cliente = c.idcliente ORDER BY v.fecha DESC, v.id DESC LIMIT 10");
    
    if ($query_recientes && mysqli_num_rows($query_recientes) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>ID Usuario</th><th>Cliente</th><th>Total</th><th>Fecha</th></tr>";
        while ($row = mysqli_fetch_assoc($query_recientes)) {
            $highlight = ($row['id_usuario'] == $id_user) ? "style='background-color: #d4edda;'" : "";
            echo "<tr $highlight>";
            echo "<td>{$row['id']}</td>";
            echo "<td><strong>{$row['id_usuario']}</strong></td>";
            echo "<td>{$row['cliente_nombre']}</td>";
            echo "<td>$" . number_format($row['total'], 2) . "</td>";
            echo "<td>{$row['fecha']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-warning'>No se encontraron ventas recientes</div>";
    }
    
    // 3. Ventas por usuario
    echo "<h2>Ventas por Usuario:</h2>";
    $query_por_usuario = mysqli_query($conexion, "SELECT id_usuario, COUNT(*) as cantidad, SUM(total) as total FROM ventas GROUP BY id_usuario ORDER BY cantidad DESC");
    
    if ($query_por_usuario && mysqli_num_rows($query_por_usuario) > 0) {
        echo "<table>";
        echo "<tr><th>ID Usuario</th><th>Cantidad de Ventas</th><th>Total</th></tr>";
        while ($row = mysqli_fetch_assoc($query_por_usuario)) {
            $highlight = ($row['id_usuario'] == $id_user) ? "style='background-color: #d4edda;'" : "";
            echo "<tr $highlight>";
            echo "<td><strong>{$row['id_usuario']}</strong>" . ($row['id_usuario'] == $id_user ? " ← TÚ" : "") . "</td>";
            echo "<td>{$row['cantidad']}</td>";
            echo "<td>$" . number_format($row['total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 4. Ventas del usuario actual
    if ($id_user > 0) {
        echo "<h2>Ventas del Usuario Actual (ID: $id_user):</h2>";
        $query_usuario_actual = mysqli_query($conexion, "SELECT v.id, v.id_cliente, v.total, v.fecha, c.nombre as cliente_nombre FROM ventas v LEFT JOIN cliente c ON v.id_cliente = c.idcliente WHERE v.id_usuario = $id_user ORDER BY v.fecha DESC, v.id DESC LIMIT 20");
        
        if ($query_usuario_actual && mysqli_num_rows($query_usuario_actual) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Cliente</th><th>Total</th><th>Fecha</th></tr>";
            while ($row = mysqli_fetch_assoc($query_usuario_actual)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['cliente_nombre']}</td>";
                echo "<td>$" . number_format($row['total'], 2) . "</td>";
                echo "<td>{$row['fecha']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ No se encontraron ventas para el usuario actual (ID: $id_user)</div>";
            echo "<p>Esto explica por qué no aparecen ventas en la lista. Las ventas se están guardando con un <code>id_usuario</code> diferente.</p>";
        }
    }
    
    // 5. Verificar usuarios en la base de datos
    echo "<h2>Usuarios en la Base de Datos:</h2>";
    $query_usuarios = mysqli_query($conexion, "SELECT idusuario, nombre, usuario, estado FROM usuario ORDER BY idusuario");
    
    if ($query_usuarios && mysqli_num_rows($query_usuarios) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Estado</th></tr>";
        while ($row = mysqli_fetch_assoc($query_usuarios)) {
            $highlight = ($row['idusuario'] == $id_user) ? "style='background-color: #d4edda;'" : "";
            echo "<tr $highlight>";
            echo "<td><strong>{$row['idusuario']}</strong>" . ($row['idusuario'] == $id_user ? " ← TÚ" : "") . "</td>";
            echo "<td>{$row['nombre']}</td>";
            echo "<td>{$row['usuario']}</td>";
            echo "<td>" . ($row['estado'] == 1 ? "✅ Activo" : "❌ Inactivo") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    ?>
    
    <div class="alert alert-info">
        <h3>💡 Soluciones Posibles:</h3>
        <ul>
            <li>Si las ventas tienen un <code>id_usuario</code> diferente al tuyo, verifica que estés logueado con el usuario correcto.</li>
            <li>Si las ventas tienen <code>id_usuario = NULL</code> o <code>0</code>, hay un problema al guardar las ventas.</li>
            <li>Verifica los logs del servidor para ver qué <code>id_usuario</code> se está usando al guardar ventas.</li>
        </ul>
    </div>
    
    <p><a href="lista_ventas.php">← Volver a Lista de Ventas</a></p>
</body>
</html>

