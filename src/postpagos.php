<?php
require "../conexion.php";
session_start();

// Permitir fallback entre esquemas con/sin columna descripcion.
mysqli_report(MYSQLI_REPORT_OFF);

$id_venta = isset($_POST['idventa']) ? intval($_POST['idventa']) : 0;
$monto = isset($_POST['idabona']) ? floatval($_POST['idabona']) : 0;
$id_metodo = isset($_POST['id_metodo']) ? intval($_POST['id_metodo']) : 1;

if ($id_metodo < 1) {
    $id_metodo = 1;
}

if ($id_venta <= 0 || $monto <= 0) {
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Complete venta y monto válidos',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    exit;
}

$query = mysqli_query($conexion, "SELECT * FROM postpagos WHERE id_venta = $id_venta");
$valueventa = mysqli_fetch_assoc($query);

if (mysqli_num_rows($query) == 0) {
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Venta inexistente',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    exit;
}

if ($valueventa['resto'] == 0) {
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'La venta no tiene resto que abonar',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    exit;
}

$id_cliente = intval($valueventa['id_cliente']);
$abonatabla = floatval($valueventa['abona']);
$abonatotal = $abonatabla + $monto;
$resto = floatval($valueventa['resto']);

if ($resto < $monto) {
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'El abono es mayor al resto',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    exit;
}

$resto = $resto - $monto;
$fecha = date("Y-m-d H:i:s");
$desc_ing = mysqli_real_escape_string($conexion, 'Abono venta #' . $id_venta);

function insertar_ingreso_postpago($conexion, $monto, $desc_ing, $fecha, $id_venta, $id_cliente, $id_metodo) {
    $intentos = [
        "INSERT INTO ingresos(ingresos, descripcion, fecha, id_venta, id_cliente, id_metodo) VALUES ($monto, '$desc_ing', '$fecha', $id_venta, $id_cliente, $id_metodo)",
        "INSERT INTO ingresos(ingresos, fecha, id_venta, id_cliente, id_metodo) VALUES ($monto, '$fecha', $id_venta, $id_cliente, $id_metodo)",
    ];
    foreach ($intentos as $sql) {
        if (mysqli_query($conexion, $sql)) {
            return true;
        }
    }
    return false;
}

function insertar_egreso_postpago($conexion, $monto_eg, $desc_eg, $fecha, $id_cliente, $id_metodo) {
    $intentos = [
        "INSERT INTO egresos(egresos, descripcion, fecha, id_cliente, id_metodo) VALUES ($monto_eg, '$desc_eg', '$fecha', $id_cliente, $id_metodo)",
        "INSERT INTO egresos(egresos, fecha, id_cliente, id_metodo) VALUES ($monto_eg, '$fecha', $id_cliente, $id_metodo)",
    ];
    foreach ($intentos as $sql) {
        if (mysqli_query($conexion, $sql)) {
            return true;
        }
    }
    return false;
}

mysqli_begin_transaction($conexion);

$update = mysqli_query($conexion, "UPDATE postpagos SET abona = '$abonatotal', resto = '$resto' WHERE id_venta = '$id_venta'");
$update2 = mysqli_query($conexion, "UPDATE ventas SET abona = '$abonatotal', resto = '$resto' WHERE id = '$id_venta'");
$update3 = mysqli_query($conexion, "UPDATE detalle_venta SET abona = '$abonatotal', resto = '$resto' WHERE id_venta = '$id_venta'");

$ing = insertar_ingreso_postpago($conexion, $monto, $desc_ing, $fecha, $id_venta, $id_cliente, $id_metodo);

$egr_ok = true;
if ($id_metodo === 5) {
    $desc_eg = mysqli_real_escape_string($conexion, 'Transf. laboratorio (abono venta #' . $id_venta . ')');
    $monto_eg = -abs($monto);
    $egr_ok = insertar_egreso_postpago($conexion, $monto_eg, $desc_eg, $fecha, $id_cliente, 5);
}

if ($update !== false && $update2 !== false && $update3 !== false && $ing !== false && $egr_ok) {
    mysqli_commit($conexion);
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'success',
        title: 'Abono realizado',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    echo "<br><br><br><div class='row justify-content-center'><div class='alert alert-success w-20'><div class='col-md-12 text-center'>VER PDF</div></div></div>";
    echo "<div class='row justify-content-center'>
                    <a href='pdf/generar.php?cl=$id_cliente&v=$id_venta' target='_blank' class='btn btn-danger'><i class='fas fa-file-pdf'></i></a>
                <div>";
} else {
    mysqli_rollback($conexion);
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Error al registrar el abono',
        showConfirmButton: false,
        timer: 2000
    })</script>";
}
