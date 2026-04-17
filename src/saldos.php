<?php
session_start();
require "../conexion.php";

// En este flujo usamos fallback entre esquemas distintos; no queremos cortar con excepción.
mysqli_report(MYSQLI_REPORT_OFF);

if (!isset($_SESSION['idUser'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Sesión',text:'Iniciá sesión de nuevo',icon:'warning',timer:3000});</script>";
    exit;
}

if (!$conexion) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Base de datos',text:'No hay conexión a la base de datos',icon:'error',timer:4000});</script>";
    exit;
}

header('Content-Type: text/html; charset=utf-8');

$valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$descripcion_otro = isset($_POST['descripcion_otro']) ? trim($_POST['descripcion_otro']) : '';
$idmetodo = isset($_POST['id_metodo']) ? intval($_POST['id_metodo']) : 1;

if ($idmetodo < 1) {
    $idmetodo = 1;
}

if ($valor == 0) {
    echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Error',text:'El valor no puede ser 0',icon:'error',timer:2000});</script>";
    exit;
}

if ($descripcion === 'otros') {
    if ($descripcion_otro === '') {
        echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Complete el detalle',text:'Indique en qué consiste Otros',icon:'warning',timer:2500});</script>";
        exit;
    }
    $descripcion = 'Otros: ' . $descripcion_otro;
}

$etiqueta = $descripcion;
$desc_sql = mysqli_real_escape_string($conexion, $etiqueta);
$fecha = date("Y-m-d");
$idcliente = 0;
$valor_sql = mysqli_real_escape_string($conexion, (string) $valor);
$id_venta_cero = mysqli_real_escape_string($conexion, '0');
$id_venta_manual = mysqli_real_escape_string($conexion, 'MANUAL|' . $etiqueta);

/**
 * Esquemas distintos: con/sin columna descripcion; id_venta texto para movimientos manuales.
 */
function insertar_ingreso_saldos($conexion, $valor_sql, $desc_sql, $fecha, $idcliente, $idmetodo, $id_venta_cero, $id_venta_manual) {
    $intentos = [
        "INSERT INTO ingresos (ingresos, descripcion, fecha, id_venta, id_cliente, id_metodo) VALUES ('$valor_sql', '$desc_sql', '$fecha', '$id_venta_cero', $idcliente, $idmetodo)",
        "INSERT INTO ingresos (ingresos, fecha, id_venta, id_cliente, id_metodo) VALUES ('$valor_sql', '$fecha', '$id_venta_manual', $idcliente, $idmetodo)",
        "INSERT INTO ingresos (ingresos, fecha, id_venta, id_cliente, id_metodo) VALUES ('$valor_sql', '$fecha', '$id_venta_cero', $idcliente, $idmetodo)",
    ];
    foreach ($intentos as $sql) {
        if (mysqli_query($conexion, $sql)) {
            return true;
        }
    }
    return false;
}

function insertar_egreso_saldos($conexion, $valor_neg_sql, $desc_sql, $fecha, $idcliente, $idmetodo) {
    $intentos = [
        "INSERT INTO egresos (egresos, descripcion, fecha, id_cliente, id_metodo) VALUES ('$valor_neg_sql', '$desc_sql', '$fecha', $idcliente, $idmetodo)",
        "INSERT INTO egresos (egresos, fecha, id_cliente, id_metodo) VALUES ('$valor_neg_sql', '$fecha', $idcliente, $idmetodo)",
    ];
    foreach ($intentos as $sql) {
        if (mysqli_query($conexion, $sql)) {
            return true;
        }
    }
    return false;
}

if ($tipo === 'ingreso') {
    $ok = insertar_ingreso_saldos($conexion, $valor_sql, $desc_sql, $fecha, $idcliente, $idmetodo, $id_venta_cero, $id_venta_manual);
    if ($ok) {
        echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Ingreso agregado',text:'El ingreso se ha agregado correctamente',icon:'success',timer:2500});</script>";
    } else {
        error_log('saldos ingreso: ' . mysqli_error($conexion));
        echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Error',text:'No se pudo registrar el ingreso',icon:'error',timer:3500});</script>";
    }
} elseif ($tipo === 'egreso') {
    $valor_neg = -abs($valor);
    $valor_neg_sql = mysqli_real_escape_string($conexion, (string) $valor_neg);
    $ok = insertar_egreso_saldos($conexion, $valor_neg_sql, $desc_sql, $fecha, $idcliente, $idmetodo);
    if ($ok) {
        echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Egreso agregado',text:'El egreso se ha agregado correctamente',icon:'success',timer:2500});</script>";
    } else {
        error_log('saldos egreso: ' . mysqli_error($conexion));
        echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Error',text:'No se pudo registrar el egreso',icon:'error',timer:3500});</script>";
    }
} else {
    echo "<script>Swal.fire({position:'top-end',showConfirmButton:false,title:'Error',text:'Tipo de movimiento no válido',icon:'error',timer:2000});</script>";
}
