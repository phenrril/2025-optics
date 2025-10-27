<?php
require_once '../../conexion.php';
require_once 'fpdf/fpdf.php';
$pdf = new FPDF('P', 'mm', 'letter');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetTitle("Ventas");
$pdf->SetFont('Arial', 'B', 12);
$total = 0;
$id = $_GET['v'];
$idcliente = $_GET['cl'];
$fecha = mysqli_query($conexion, "SELECT fecha FROM ventas WHERE id = '$id'"); 
$fechaactual = mysqli_fetch_assoc($fecha);
$nuevo_formato = date("d-m-Y", strtotime($fechaactual['fecha']));
$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$gradu= mysqli_query($conexion, "SELECT * FROM graduaciones where id_venta='$id'");
$datos = mysqli_fetch_assoc($config);
$datos44 = mysqli_fetch_assoc($gradu);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$datosC = mysqli_fetch_assoc($clientes);
$ventas = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id");
$ventas2= mysqli_query($conexion, "SELECT * FROM detalle_venta where id_venta='$id'");
$postapagos = mysqli_query($conexion, "SELECT * FROM postpagos where id_venta='$id'");
$idventas = mysqli_fetch_assoc($ventas2);
$idpostapagos = mysqli_fetch_assoc($postapagos);
$pdf->Cell(195, 5, utf8_decode($datos['nombre']), 0, 1, 'C'); //
$pdf->Image("../../assets/img/logo.png", 180, 10, 30, 30, 'PNG');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5,  utf8_decode("Teléfono: "), 0, 0, 'L'); //
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, $datos['telefono'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5,  utf8_decode("Fecha: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, $nuevo_formato , 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, utf8_decode("Dirección: "), 0, 0, 'L'); //
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, utf8_decode($datos['direccion']), 0, 1, 'L'); //
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, "Correo: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, utf8_decode($datos['email']), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(22, 5, "ID Venta: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, utf8_decode($idventas['id_venta']), 0, 1, 'L');
if($idventas['idcristal'] == 0){
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(22, 5, "ID Cristales: ", 0, 0, 'L');
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(20, 5, "No asignado", 0, 0, 'L');
  $pdf->Ln(3);
}
else
{
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(22, 5, "ID Cristales: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, utf8_decode($idventas['idcristal']), 0, 1, 'L');
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(196, 5, "Datos del cliente", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(60, 5,utf8_decode('Nombre'), 0, 0, 'L'); //  
$pdf->Cell(40, 5, utf8_decode('Teléfono'), 0, 0, 'L'); //
$pdf->Cell(40, 5, utf8_decode('Dirección'), 0, 0, 'L');
$pdf->Cell(40, 5, utf8_decode('Obra Social'), 0, 1, 'L'); //
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, utf8_decode($datosC['nombre']), 0, 0, 'L'); //
$pdf->Cell(40, 5, utf8_decode($datosC['telefono']), 0, 0, 'L'); // utf8_decode
$pdf->Cell(40, 5, utf8_decode($datosC['direccion']), 0, 0, 'L');
$pdf->Cell(40, 5, utf8_decode($datosC['obrasocial']), 0, 1, 'L'); //
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(196, 5, "Detalle de Producto", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(14, 5, utf8_decode('N°'), 0, 0, 'L');
$pdf->Cell(62, 5, utf8_decode('Descripción'), 0, 0, 'L');
$pdf->Cell(25, 5, 'Cantidad', 0, 0, 'L');
$pdf->Cell(22, 5, 'Precio orig', 0, 0, 'L');
$pdf->Cell(42, 5, 'Precio c/dto', 0, 0, 'L');
$pdf->Cell(35, 5, 'Sub Total.', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$contador = 1;
while ($row = mysqli_fetch_assoc($ventas)) {
    $pdf->Cell(14, 5, $contador, 0, 0, 'L');
    $pdf->Cell(62, 5, $row['descripcion'], 0, 0, 'L');
    $pdf->Cell(25, 5, $row['cantidad'], 0, 0, 'L');
    $pdf->Cell(22, 5, $row['precio_original'], 0, 0, 'L');
    $pdf->Cell(42, 5, $row['precio'], 0, 0, 'L');    
    $pdf->Cell(35, 5, number_format($row['cantidad'] * $row['precio'], 2, '.', ','), 0, 1, 'L');
    $total += $row['cantidad'] * $row['precio'];
    $contador++;
}

$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
if(($idventas['obrasocial']) == 0){

}else{

$pdf->Cell(165, 5, "Obra Social $", 0, 0, 'R');
$pdf->Cell(35, 5, number_format(($idventas['obrasocial']), 2, '.', ','), 0, 1, 'L');
$pdf->Ln(3);
}
$pdf->Cell(165, 5, "Abona $", 0, 0, 'R');
$pdf->Cell(35, 5, number_format(($idpostapagos['abona']), 2, '.', ','), 0, 1, 'L');
$pdf->Ln(3);
$pdf->Cell(165, 5, "Total $", 0, 0, 'R');
$pdf->Cell(35, 5, number_format($total, 2, '.', ','), 0, 1, 'L');
$pdf->Ln(3);
if(($idpostapagos['abona']) == $total){
}else{ 
$pdf->Cell(165, 5, "Resto $", 0, 0, 'R');
$pdf->Cell(35, 5, number_format(($idpostapagos['resto']), 2, '.', ','), 0, 1, 'L');
$pdf->Ln(4);
}
if ($datos44 != ""){
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(196, 5, "Graduaciones", 1, 1, 'C', 1);
$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0);
}
if ($datos44 != ""){
  mysqli_data_seek($gradu,0);
  while ($datos44 = mysqli_fetch_assoc($gradu)){

// Dibujar dos celdas en la misma fila para Ojo Derecho C y Ojo Derecho L
$pdf->Cell(60, 5, ('ADD  '), 0, 0, 'R');
$pdf->Cell(15, 5, ($datos44['addg']), 1, 0, 'C');
$pdf->Ln(8);
$pdf->Cell(93, 5, ('LEJOS'), 0, 0, 'L');
$pdf->Cell(90, 5, ('CERCA'), 0, 0, 'L');
$pdf->Ln(8);
$pdf->Cell(30, 5, ('Ojo Derecho L  '), 0, 0, 'L');
$pdf->Cell(15, 5, ($datos44['od_l_1']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['od_l_2']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['od_c_3']), 1, 0, 'C');
$pdf->Cell(45, 5, ('Ojo Derecho C'), 0, 0, 'R');
$pdf->Cell(15, 5, ($datos44['od_c_1']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['od_c_2']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['od_l_3']), 1, 0, 'C');
$pdf->Ln(8);
$pdf->Cell(30, 5, ('Ojo Izquierdo L '), 0, 0, 'L');
$pdf->Cell(15, 5, ($datos44['oi_l_1']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['oi_l_2']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['oi_l_3']), 1, 0, 'C');
$pdf->Cell(45, 5, ('Ojo Izquierdo C'), 0, 0, 'R');
$pdf->Cell(15, 5, ($datos44['oi_c_1']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['oi_c_2']), 1, 0, 'C');
$pdf->Cell(15, 5, ($datos44['oi_c_3']), 1, 0, 'C');
$pdf->Ln(10);
$pdf->Cell(45, 5, ('Observaciones:'), 0, 0, 'L');
$pdf->Cell(25, 5, ($datos44['obs']), 0, 0, 'R');
$pdf->Ln(10);
}
}
$pdf->Output("ventas.pdf", "I");

?>

