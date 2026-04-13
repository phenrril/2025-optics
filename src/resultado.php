<?php
require_once "../conexion.php";
session_start();

function gt_post($conexion, $key, $default = '0')
{
    if (!isset($_POST[$key])) {
        return $default;
    }
    $v = $_POST[$key];
    if ($v === '') {
        return $default;
    }
    return mysqli_real_escape_string($conexion, (string) $v);
}

$ojolD1 = gt_post($conexion, 'ojoDl1', '0');
$ojolD2 = gt_post($conexion, 'ojoDl2', '0');
$ojolD3 = gt_post($conexion, 'ojoDl3', '0');
$ojolI1 = gt_post($conexion, 'ojoIl1', '0');
$ojolI2 = gt_post($conexion, 'ojoIl2', '0');
$ojolI3 = gt_post($conexion, 'ojoIl3', '0');
$ojoD1 = gt_post($conexion, 'ojoD1', '0');
$ojoD2 = gt_post($conexion, 'ojoD2', '0');
$ojoD3 = gt_post($conexion, 'ojoD3', '0');
$ojoI1 = gt_post($conexion, 'ojoI1', '0');
$ojoI2 = gt_post($conexion, 'ojoI2', '0');
$ojoI3 = gt_post($conexion, 'ojoI3', '0');
$add1 = gt_post($conexion, 'add', '0');

if ($ojolD1 === '0' && $ojolD2 === '0' && $ojolD3 === '0' && $ojolI1 === '0' && $ojolI2 === '0' && $ojolI3 === '0' && $ojoD1 === '0' && $ojoD2 === '0' && $ojoD3 === '0' && $ojoI1 === '0' && $ojoI2 === '0' && $ojoI3 === '0' && $add1 === '0') {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        title: 'No se puede guardar un registro vacio',
        showConfirmButton: false,
        timer: 2000
    })</script>;";
    die();
}

$id_user2 = (int) $_SESSION['idUser'];

$obs = gt_post($conexion, 'obs', '');
if ($obs === '') {
    $obs = mysqli_real_escape_string($conexion, 'Sin Observaciones');
}

$id_edit = isset($_POST['id_graduacion_edit']) ? (int) $_POST['id_graduacion_edit'] : 0;

if ($id_edit > 0) {
    $query = mysqli_query(
        $conexion,
        "UPDATE graduaciones_temp SET od_l_1='$ojolD1', od_l_2='$ojolD2', od_l_3='$ojolD3', oi_l_1='$ojolI1', oi_l_2='$ojolI2', oi_l_3='$ojolI3', od_c_1='$ojoD1', od_c_2='$ojoD2', od_c_3='$ojoD3', oi_c_1='$ojoI1', oi_c_2='$ojoI2', oi_c_3='$ojoI3', addg='$add1', obs='$obs' WHERE id=$id_edit AND id_usuario=$id_user2"
    );
    $msg_ok = 'Graduacion actualizada correctamente';
    $msg_err = 'Error al actualizar graduacion';
} else {
    $query = mysqli_query(
        $conexion,
        "INSERT INTO graduaciones_temp(od_l_1, od_l_2, od_l_3, oi_l_1, oi_l_2, oi_l_3, od_c_1, od_c_2, od_c_3, oi_c_1, oi_c_2, oi_c_3, addg, id_usuario, obs) VALUES ('$ojolD1', '$ojolD2', '$ojolD3', '$ojolI1', '$ojolI2', '$ojolI3', '$ojoD1', '$ojoD2', '$ojoD3', '$ojoI1', '$ojoI2', '$ojoI3', '$add1', $id_user2, '$obs')"
    );
    $msg_ok = 'Graduacion Agregada Correctamente';
    $msg_err = 'Error al agregar Graduacion';
}

if ($query) {
    echo '<script>var ojoD1 = document.getElementById("ojoD1")</script>';
    echo '<script>ojoD1.value = ""</script>';
    echo '<script>var ojoD2 = document.getElementById("ojoD2")</script>';
    echo '<script>ojoD2.value = ""</script>';
    echo '<script>var ojoD3 = document.getElementById("ojoD3")</script>';
    echo '<script>ojoD3.value = ""</script>';
    echo '<script>var ojoI1 = document.getElementById("ojoI1")</script>';
    echo '<script>ojoI1.value = ""</script>';
    echo '<script>var ojoI2 = document.getElementById("ojoI2")</script>';
    echo '<script>ojoI2.value = ""</script>';
    echo '<script>var ojoI3 = document.getElementById("ojoI3")</script>';
    echo '<script>ojoI3.value = ""</script>';
    echo '<script>var ojoDl1 = document.getElementById("ojoDl1")</script>';
    echo '<script>ojoDl1.value = ""</script>';
    echo '<script>var ojoDl2 = document.getElementById("ojoDl2")</script>';
    echo '<script>ojoDl2.value = ""</script>';
    echo '<script>var ojoDl3 = document.getElementById("ojoDl3")</script>';
    echo '<script>ojoDl3.value = ""</script>';
    echo '<script>var ojoIl1 = document.getElementById("ojoIl1")</script>';
    echo '<script>ojoIl1.value = ""</script>';
    echo '<script>var ojoIl2 = document.getElementById("ojoIl2")</script>';
    echo '<script>ojoIl2.value = ""</script>';
    echo '<script>var ojoIl3 = document.getElementById("ojoIl3")</script>';
    echo '<script>ojoIl3.value = ""</script>';
    echo '<script>var add1 = document.getElementById("add")</script>';
    echo '<script>add1.value = ""</script>';
    echo '<script>var obs = document.getElementById("obs")</script>';
    echo '<script>obs.value = ""</script>';
    echo '<script>var hid = document.getElementById("id_graduacion_edit"); if (hid) hid.value = "";</script>';
    echo '<script>var gbtn = document.getElementById("grad"); if (gbtn) { gbtn.innerHTML = \'<i class="fas fa-plus mr-2"></i> Agregar Graduaciones\'; }</script>';
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: " . json_encode($msg_ok) . ",
        showConfirmButton: false,
        timer: 2000
    })</script>;";

    $id_usuario = $id_user2;
    include __DIR__ . '/render_graduaciones_temp.php';
} else {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        title: " . json_encode($msg_err) . ",
        showConfirmButton: false,
        timer: 2000
    })</script>;";
}
