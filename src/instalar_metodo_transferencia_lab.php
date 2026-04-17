<?php
/**
 * Un solo uso: inserta metodos.id = 5 "Transferencia laboratorio".
 * Misma autorización que configuracion_sistema.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../conexion.php';

if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_user = (int) $_SESSION['idUser'];
$permiso = 'configuracion';
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query(
    $conexion,
    "SELECT p.id FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'"
);
if ((!$sql || mysqli_num_rows($sql) === 0) && $id_user !== 1) {
    echo json_encode(['success' => false, 'message' => 'Sin permiso']);
    exit;
}

$ya = mysqli_query($conexion, 'SELECT id FROM metodos WHERE id = 5 LIMIT 1');
if ($ya && mysqli_num_rows($ya) > 0) {
    echo json_encode(['success' => true, 'message' => 'El método de pago ya estaba registrado.', 'already' => true]);
    exit;
}

$desc = mysqli_real_escape_string($conexion, 'Transferencia laboratorio');
$ok = mysqli_query($conexion, "INSERT INTO metodos (id, descripcion) VALUES (5, '$desc')");

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Método de pago agregado correctamente.']);
    exit;
}

$errno = mysqli_errno($conexion);
// Duplicado u otra condición de id ya existente
if ($errno === 1062) {
    echo json_encode(['success' => true, 'message' => 'El método de pago ya existía.', 'already' => true]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Error al insertar: ' . mysqli_error($conexion),
]);
