<?php 
require "../conexion.php";
session_start();

$id_venta = intval($_POST['idanular']);

$facturada = mysqli_query($conexion, "SELECT 1 FROM facturas_electronicas WHERE id_venta = $id_venta AND estado = 'aprobado' LIMIT 1");
if ($facturada && mysqli_num_rows($facturada) > 0) {
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'No se puede anular',
        text: 'Esta venta ya fue facturada ante AFIP y no puede modificarse ni eliminarse.',
        showConfirmButton: false,
        timer: 3000
    })</script>;";
    exit;
}

$consultaDetalle = mysqli_query($conexion, "SELECT * FROM detalle_venta WHERE id_venta = $id_venta");
$numFilas = mysqli_num_rows($consultaDetalle);

if($numFilas > 0)
{
while ($row = mysqli_fetch_assoc($consultaDetalle)) {
    $id_producto = $row['id_producto'];
    $cantidad = $row['cantidad'];
    $stockActual = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
    $stockNuevo = mysqli_fetch_assoc($stockActual);
    $stockTotal = $stockNuevo['existencia'] + $cantidad;
    $stock = mysqli_query($conexion, "UPDATE producto SET existencia = $stockTotal WHERE codproducto = $id_producto");
    
    // Reactivar producto automáticamente cuando se restaura stock y queda mayor que 0
    if ($stockTotal > 0) {
        $reactivar = mysqli_query($conexion, "UPDATE producto SET estado = 1 WHERE codproducto = $id_producto");
    }
} 
$eliminarDet = mysqli_query($conexion, "DELETE FROM detalle_venta WHERE id_venta = $id_venta");
$eliminarPost = mysqli_query($conexion, "DELETE FROM postpagos WHERE id_venta = $id_venta");
$eliminarGrad = mysqli_query($conexion, "DELETE FROM graduaciones WHERE id_venta = $id_venta");
$eliminar = mysqli_query($conexion, "DELETE FROM ventas WHERE id = $id_venta");

echo "<script>Swal.fire({
    position: 'top-mid',
    icon: 'success',
    title: 'Venta Eliminada',
    showConfirmButton: false,
    timer: 2000
})</script>;";
}
else 
{
    echo "<script>Swal.fire({
        position: 'top-mid',
        icon: 'error',
        title: 'Error al eliminar venta, verifique ID',
        showConfirmButton: false,
        timer: 3000
    })</script>;";
}

?>