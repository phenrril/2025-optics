<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: historia_clinica.php");
    exit();
}

// Eliminar la historia clínica
$query = "DELETE FROM historia_clinica WHERE id = $id";

if (mysqli_query($conexion, $query)) {
    header("Location: historia_clinica.php?success=eliminado");
} else {
    header("Location: historia_clinica.php?error=eliminar");
}

exit();
?>

