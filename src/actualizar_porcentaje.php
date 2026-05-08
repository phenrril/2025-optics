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
$porcentaje = isset($_POST['id_porcentaje']) ? floatval($_POST['id_porcentaje']) : 0;
$modo = isset($_POST['id_modo']) ? $_POST['id_modo'] : 'aumentar';

if ($marca === '' || !isset($_POST['id_porcentaje']) || $_POST['id_porcentaje'] === '') {
    echo json_encode([
        'ok' => false,
        'message' => 'Complete ambos campos'
    ]);
    exit;
}

if ($porcentaje < 0 || $porcentaje > 100) {
    echo json_encode([
        'ok' => false,
        'message' => 'El porcentaje debe estar entre 0 y 100'
    ]);
    exit;
}

if (!in_array($modo, ['aumentar', 'descontar'], true)) {
    echo json_encode([
        'ok' => false,
        'message' => 'Modo inválido'
    ]);
    exit;
}

$factor = ($modo === 'descontar')
    ? (1 - ($porcentaje / 100))
    : (1 + ($porcentaje / 100));

if ($factor < 0) {
    echo json_encode([
        'ok' => false,
        'message' => 'El factor resultante no es válido'
    ]);
    exit;
}

$marcaNormalizada = strtolower($marca);
$marcaExpr = "LOWER(TRIM(REGEXP_REPLACE(marca, '[[:space:]]+', ' ')))";

if ($accion === 'preview') {
    $sqlPreview = "SELECT COUNT(*) AS total,
                          AVG(precio) AS promedio_actual,
                          AVG(ROUND(precio * ?, 2)) AS promedio_nuevo
                   FROM producto
                   WHERE $marcaExpr = ? AND estado = 1";
    $stmtPreview = mysqli_prepare($conexion, $sqlPreview);

    if ($stmtPreview === false) {
        echo json_encode([
            'ok' => false,
            'message' => 'Error interno al preparar la previsualización'
        ]);
        exit;
    }

    mysqli_stmt_bind_param($stmtPreview, "ds", $factor, $marcaNormalizada);
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
    if ($total <= 0) {
        echo json_encode([
            'ok' => false,
            'message' => 'No se encontraron productos activos para esa marca'
        ]);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'message' => 'Previsualización generada',
        'total' => $total,
        'modo' => $modo,
        'porcentaje' => $porcentaje,
        'promedio_actual' => number_format((float)$dataPreview['promedio_actual'], 2, '.', ''),
        'promedio_nuevo' => number_format((float)$dataPreview['promedio_nuevo'], 2, '.', '')
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
              SET precio = ROUND(precio * ?, 2)
              WHERE $marcaExpr = ? AND estado = 1";
$stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);

if ($stmtUpdate === false) {
    echo json_encode([
        'ok' => false,
        'message' => 'Error interno al preparar la actualización'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmtUpdate, "ds", $factor, $marcaNormalizada);
$executedUpdate = mysqli_stmt_execute($stmtUpdate);

if (!$executedUpdate) {
    mysqli_stmt_close($stmtUpdate);
    echo json_encode([
        'ok' => false,
        'message' => 'Error al actualizar precios'
    ]);
    exit;
}

$updatedRows = mysqli_stmt_affected_rows($stmtUpdate);
mysqli_stmt_close($stmtUpdate);

if ($updatedRows <= 0) {
    $sqlCount = "SELECT COUNT(*) AS total
                 FROM producto
                 WHERE $marcaExpr = ? AND estado = 1";
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
                'message' => 'No hubo cambios en los precios (mismo valor final)',
                'updated' => 0
            ]);
            exit;
        }
    }

    echo json_encode([
        'ok' => false,
        'message' => 'No se encontraron productos activos para esa marca'
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'message' => ($modo === 'descontar') ? 'Marca descontada correctamente' : 'Marca actualizada correctamente',
    'updated' => $updatedRows
]);
?>
