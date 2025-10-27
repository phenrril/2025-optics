<?php 
require "../conexion.php";
session_start();
$id_venta = $_POST['idventa'];
$id_cristal = $_POST['idcristal'];
if($id_venta == "" || $id_cristal == ""){
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Complete ambos campos',
        showConfirmButton: false,
        timer: 2000
    })</script>";
    exit;
}
$query = mysqli_query($conexion, "SELECT * FROM ventas WHERE id = $id_venta");

if (mysqli_num_rows($query) > 0) {
$valueventa = mysqli_fetch_assoc($query);
$id_cliente = $valueventa['id_cliente'];
$update = mysqli_query($conexion, "UPDATE detalle_venta SET idcristal = $id_cristal WHERE id_venta = $id_venta");
if($update){
    $result = mysqli_affected_rows($conexion);
    if($result > 0){
        echo "<script>Swal.fire({
            position: 'top-mid',
            icon: 'success',
            title: 'Cristal agregado',
            showConfirmButton: false,
            timer: 2000
        })</script>;";
        echo "<br><br><br><div class='row justify-content-center'><div class='alert alert-success w-20'><div class='col-md-12 text-center'>VER PDF</div></div></div>";
        echo "  <div class='row justify-content-center'>
                    <a href='pdf/generar.php?cl=$id_cliente&v=$id_venta' target='_blank' class='btn btn-danger'><i class='fas fa-file-pdf'></i></a>
                <div>";    
    }
        else{
            echo "<script>Swal.fire({
                position: 'top-mid',
                icon: 'error',
                title: 'Venta inexistente',
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
        title: 'Venta inexistente',
        showConfirmButton: false,
        timer: 2000
    })</script>;";
}

?>
