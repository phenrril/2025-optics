<?php
require "../conexion.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!($conexion instanceof mysqli)) {
    echo json_encode([
        'ok' => false,
        'message' => 'Error de conexión a base de datos'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'ok' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

$accion = isset($_POST['accion']) ? $_POST['accion'] : 'apply';
$marca = isset($_POST['id_marca']) ? preg_replace('/\s+/', ' ', trim($_POST['id_marca'])) : '';
$modo = isset($_POST['id_modo_costo']) ? $_POST['id_modo_costo'] : 'marcar';

if ($marca === '') {
    echo json_encode([
        'ok' => false,
        'message' => 'Ingrese la marca'
    ]);
    exit;
}

if (!in_array($modo, ['marcar', 'desmarcar'], true)) {
    echo json_encode([
        'ok' => false,
        'message' => 'Modo inválido'
    ]);
    exit;
}

$costoValor = ($modo === 'marcar') ? 1 : 0;
$marcaNormalizada = strtolower($marca);
$marcaExpr = "LOWER(TRIM(REGEXP_REPLACE(marca, '[[:space:]]+', ' ')))";

if ($accion === 'preview') {
    $sqlPreview = "SELECT COUNT(*) AS total
                   FROM producto
                   WHERE $marcaExpr = ? AND costo <> ?";
    $stmtPreview = mysqli_prepare($conexion, $sqlPreview);

    if ($stmtPreview === false) {
        echo json_encode([
            'ok' => false,
            'message' => 'Error interno al preparar la previsualización'
        ]);
        exit;
    }

    mysqli_stmt_bind_param($stmtPreview, "si", $marcaNormalizada, $costoValor);
    $executedPreview = mysqli_stmt_execute($stmtPreview);

    if (!$executedPreview) {
        mysqli_stmt_close($stmtPreview);
        echo json_encode([
            'ok' => false,
            'message' => 'Error al previsualizar'
        ]);
        exit;
    }

    $resultPreview = mysqli_stmt_get_result($stmtPreview);
    $dataPreview = mysqli_fetch_assoc($resultPreview);
    mysqli_stmt_close($stmtPreview);

    $total = isset($dataPreview['total']) ? intval($dataPreview['total']) : 0;

    echo json_encode([
        'ok' => true,
        'message' => 'Previsualización generada',
        'total' => $total,
        'modo' => $modo
    ]);
    exit;
}

if ($accion !== 'apply') {
    echo json_encode([
        'ok' => false,
        'message' => 'Acción inválida'
    ]);
    exit;
}

$sqlUpdate = "UPDATE producto
              SET costo = ?
              WHERE $marcaExpr = ?";
$stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);

if ($stmtUpdate === false) {
    echo json_encode([
        'ok' => false,
        'message' => 'Error interno al preparar la actualización'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmtUpdate, "is", $costoValor, $marcaNormalizada);
$executedUpdate = mysqli_stmt_execute($stmtUpdate);

if (!$executedUpdate) {
    mysqli_stmt_close($stmtUpdate);
    echo json_encode([
        'ok' => false,
        'message' => 'Error al actualizar productos'
    ]);
    exit;
}

$updatedRows = mysqli_stmt_affected_rows($stmtUpdate);
mysqli_stmt_close($stmtUpdate);

if ($updatedRows <= 0) {
    $sqlCount = "SELECT COUNT(*) AS total
                 FROM producto
                 WHERE $marcaExpr = ?";
    $stmtCount = mysqli_prepare($conexion, $sqlCount);
    if ($stmtCount !== false) {
        mysqli_stmt_bind_param($stmtCount, "s", $marcaNormalizada);
        mysqli_stmt_execute($stmtCount);
        $resultCount = mysqli_stmt_get_result($stmtCount);
        $dataCount = mysqli_fetch_assoc($resultCount);
        mysqli_stmt_close($stmtCount);

        $coincidencias = isset($dataCount['total']) ? intval($dataCount['total']) : 0;
        if ($coincidencias > 0) {
            echo json_encode([
                'ok' => true,
                'message' => 'No hubo cambios (todos los productos ya tenían ese estado)',
                'updated' => 0
            ]);
            exit;
        }
    }

    echo json_encode([
        'ok' => false,
        'message' => 'No se encontraron productos para esa marca'
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'message' => ($modo === 'marcar') ? 'Productos marcados como costo (en negro)' : 'Productos desmarcados como costo',
    'updated' => $updatedRows
]);
?>
