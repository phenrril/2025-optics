<?php 
require "../conexion.php";
session_start();
$id_marca = $_POST['id_marca'];
$id_porc = $_POST['id_porcentaje'];

if($id_marca == "" || $id_porc == ""){
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Complete ambos campos',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    exit;
}
$query = mysqli_query($conexion, "SELECT * FROM producto WHERE marca = '$id_marca'");

if (mysqli_num_rows($query) > 0) {
$valueventa = mysqli_fetch_assoc($query);
//$precio = $valueventa['precio'];
//$prueba=1 +($id_porc/100);
$precio_actualizado=1 +($id_porc/100);
$update = mysqli_query($conexion, "UPDATE producto SET precio = precio * $precio_actualizado WHERE marca = '$id_marca'");
if($update){
    $result = mysqli_affected_rows($conexion);
    if($result > 0){
        echo "<script>Swal.fire({
            position: 'top-mid',
            icon: 'success',
            title: 'Marca Actualizada',
            showConfirmButton: false,
            timer: 2000
        })</script>;";
    }
        else{
            echo "<script>Swal.fire({
                position: 'top-mid',
                icon: 'error',
                title: 'Marca inexistente',
                showConfirmButton: false,
                timer: 2000
            })</script>;";
        }
    }
}
else{
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Marca inexistente',
        showConfirmButton: false,
        timer: 2000
    })</script>;";
}
echo $id_marca;
echo $id_porc;
?>
