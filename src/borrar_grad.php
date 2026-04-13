<?php
require_once "../conexion.php";
session_start();
$id_user = (int) $_SESSION['idUser'];
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        title: 'Graduación no válida',
        showConfirmButton: false,
        timer: 2000
    })</script>;";
    exit;
}

$eliminar = mysqli_query($conexion, "DELETE FROM graduaciones_temp WHERE id = $id AND id_usuario = $id_user");
if ($eliminar) {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Graduación eliminada',
        showConfirmButton: false,
        timer: 2000
    })</script>;";
    $id_usuario = $id_user;
    include __DIR__ . '/render_graduaciones_temp.php';
} else {
    echo "<script>Swal.fire({
        position: 'top-end',
        icon: 'error',
        title: 'Error al borrar graduación',
        showConfirmButton: false,
        timer: 2000
    })</script>;";
}
