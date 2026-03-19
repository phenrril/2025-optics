<?php
session_start();
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header('HTTP/1.1 403 Forbidden');
    die('Acceso denegado. Debe iniciar sesión para ver este documento.');
}

require_once '../../conexion.php';
require_once 'fpdf/fpdf.php';
$pdf = new FPDF('P', 'mm', 'letter');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetTitle("Ventas");
$total = 0;

// Helpers de estilo/formatos para mantener un diseño consistente.
function money($amount)
{
    return '$ ' . number_format((float)$amount, 2, '.', ',');
}

function sectionTitle($pdf, $title)
{
    $pdf->SetFillColor(20, 24, 34);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(196, 7, utf8_decode($title), 0, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(1);
}

function labelValue($pdf, $label, $value, $x, $y, $labelW = 24)
{
    $pdf->SetXY($x, $y);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($labelW, 5, utf8_decode($label), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 5, utf8_decode((string)$value), 0, 1, 'L');
}

function textOrDash($value)
{
    $clean = trim((string)$value);
    return $clean === '' ? '-' : $clean;
}

function shortText($value, $max = 34)
{
    $text = textOrDash($value);
    return strlen($text) > $max ? substr($text, 0, $max - 3) . '...' : $text;
}

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
$metodop = mysqli_query($conexion, "SELECT metodos.descripcion from metodos inner join ventas on ventas.id_metodo = metodos.id where ventas.id = '$id'");
$metodopago = mysqli_fetch_assoc($metodop);

$idCristal = ((int)$idventas['idcristal'] === 0) ? 'No asignado' : $idventas['idcristal'];

// Encabezado principal
$pdf->SetFillColor(245, 247, 250);
$pdf->Rect(10, 10, 196, 42, 'F');
$pdf->Image("../../assets/img/logo.png", 170, 14, 28, 0, 'PNG');
$pdf->SetXY(10, 13);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(155, 8, utf8_decode($datos['nombre']), 0, 1, 'C');
$pdf->SetX(10);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(90, 90, 90);
$pdf->Cell(155, 5, utf8_decode('Recibo de venta'), 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

labelValue($pdf, 'Telefono:', $datos['telefono'], 14, 25, 22);
labelValue($pdf, 'Fecha:', $nuevo_formato, 14, 30, 22);
labelValue($pdf, 'Direccion:', shortText($datos['direccion'], 42), 14, 35, 22);
labelValue($pdf, 'Correo:', shortText($datos['email'], 42), 14, 40, 22);
labelValue($pdf, 'ID Venta:', $idventas['id_venta'], 112, 25, 24);
labelValue($pdf, 'ID Cristales:', $idCristal, 112, 30, 24);

$pdf->SetY(56);
$pdf->SetDrawColor(220, 225, 232);
$pdf->Line(10, 54, 206, 54);

// Datos del cliente
sectionTitle($pdf, 'Datos del cliente');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(237, 242, 247);
$pdf->Cell(60, 7, utf8_decode('Nombre'), 0, 0, 'L', true);
$pdf->Cell(40, 7, utf8_decode('Telefono'), 0, 0, 'L', true);
$pdf->Cell(56, 7, utf8_decode('Direccion'), 0, 0, 'L', true);
$pdf->Cell(40, 7, utf8_decode('Obra Social'), 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(250, 250, 250);
$pdf->Cell(60, 8, utf8_decode(shortText($datosC['nombre'], 30)), 0, 0, 'L', true);
$pdf->Cell(40, 8, utf8_decode(shortText($datosC['telefono'], 16)), 0, 0, 'L', true);
$pdf->Cell(56, 8, utf8_decode(shortText($datosC['direccion'], 28)), 0, 0, 'L', true);
$pdf->Cell(40, 8, utf8_decode(shortText($datosC['obrasocial'], 20)), 0, 1, 'L', true);
$pdf->Ln(3);

// Detalle de productos
sectionTitle($pdf, 'Detalle de productos');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(237, 242, 247);
$pdf->Cell(12, 7, utf8_decode('N°'), 0, 0, 'C', true);
$pdf->Cell(68, 7, utf8_decode('Descripcion'), 0, 0, 'L', true);
$pdf->Cell(20, 7, 'Cantidad', 0, 0, 'C', true);
$pdf->Cell(28, 7, 'Precio orig.', 0, 0, 'R', true);
$pdf->Cell(30, 7, 'Precio c/dto', 0, 0, 'R', true);
$pdf->Cell(38, 7, 'Sub total', 0, 1, 'R', true);

$pdf->SetFont('Arial', '', 9);
$contador = 1;
$fill = false;
while ($row = mysqli_fetch_assoc($ventas)) {
    $pdf->SetFillColor($fill ? 250 : 255, $fill ? 252 : 255, $fill ? 255 : 255);
    $pdf->Cell(12, 7, $contador, 0, 0, 'C', true);
    $pdf->Cell(68, 7, utf8_decode(shortText($row['descripcion'], 36)), 0, 0, 'L', true);
    $pdf->Cell(20, 7, $row['cantidad'], 0, 0, 'C', true);
    $pdf->Cell(28, 7, money($row['precio_original']), 0, 0, 'R', true);
    $pdf->Cell(30, 7, money($row['precio']), 0, 0, 'R', true);
    $pdf->Cell(38, 7, money($row['cantidad'] * $row['precio']), 0, 1, 'R', true);
    $total += $row['cantidad'] * $row['precio'];
    $contador++;
    $fill = !$fill;
}

$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 11);
$summaryX = 128;
$summaryWLabel = 40;
$summaryWValue = 28;
$summaryYStart = $pdf->GetY();

if ((float)$idventas['obrasocial'] > 0) {
    $pdf->SetX($summaryX);
    $pdf->Cell($summaryWLabel, 7, 'Obra Social', 0, 0, 'R');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell($summaryWValue, 7, money($idventas['obrasocial']), 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 11);
}

$pdf->SetX($summaryX);
$pdf->Cell($summaryWLabel, 8, 'Total', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($summaryWValue, 8, money($total), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetX($summaryX);
$pdf->Cell($summaryWLabel, 7, utf8_decode('Abona ' . $metodopago['descripcion']), 0, 0, 'R');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($summaryWValue, 7, money($idpostapagos['abona']), 0, 1, 'R');

if ((float)$idpostapagos['abona'] != (float)$total) {
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetX($summaryX);
    $pdf->Cell($summaryWLabel, 7, 'Resto', 0, 0, 'R');
    $pdf->Cell($summaryWValue, 7, money($idpostapagos['resto']), 0, 1, 'R');
}
$summaryYEnd = $pdf->GetY();
$pdf->SetDrawColor(220, 225, 232);
$pdf->Rect($summaryX - 4, $summaryYStart - 2, 76, ($summaryYEnd - $summaryYStart) + 5, 'D');

$pdf->Ln(4);
if ($datos44 != "") {
    sectionTitle($pdf, 'Graduaciones');
}
if ($datos44 != "") {
    mysqli_data_seek($gradu, 0);
    while ($datos44 = mysqli_fetch_assoc($gradu)) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(20, 7, 'ADD', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 7, $datos44['addg'], 1, 1, 'C');
        $pdf->Ln(1);

        $pdf->SetFillColor(237, 242, 247);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(72, 7, 'LEJOS', 0, 0, 'C', true);
        $pdf->Cell(72, 7, 'CERCA', 0, 1, 'C', true);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(30, 6, '', 0, 0, 'L');
        $pdf->Cell(14, 6, utf8_decode('Esferico'), 0, 0, 'C');
        $pdf->Cell(14, 6, utf8_decode('Cilindrico'), 0, 0, 'C');
        $pdf->Cell(14, 6, 'Eje', 0, 0, 'C');
        $pdf->Cell(30, 6, '', 0, 0, 'L');
        $pdf->Cell(14, 6, utf8_decode('Esferico'), 0, 0, 'C');
        $pdf->Cell(14, 6, utf8_decode('Cilindrico'), 0, 0, 'C');
        $pdf->Cell(14, 6, 'Eje', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(30, 7, 'Ojo Derecho L', 0, 0, 'L');
        $pdf->Cell(14, 7, $datos44['od_l_1'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['od_l_2'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['od_l_3'], 1, 0, 'C');
        $pdf->Cell(30, 7, 'Ojo Derecho C', 0, 0, 'L');
        $pdf->Cell(14, 7, $datos44['od_c_1'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['od_c_2'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['od_c_3'], 1, 1, 'C');

        $pdf->Cell(30, 7, 'Ojo Izquierdo L', 0, 0, 'L');
        $pdf->Cell(14, 7, $datos44['oi_l_1'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['oi_l_2'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['oi_l_3'], 1, 0, 'C');
        $pdf->Cell(30, 7, 'Ojo Izquierdo C', 0, 0, 'L');
        $pdf->Cell(14, 7, $datos44['oi_c_1'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['oi_c_2'], 1, 0, 'C');
        $pdf->Cell(14, 7, $datos44['oi_c_3'], 1, 1, 'C');

        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(26, 6, 'Observaciones:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(170, 6, utf8_decode(shortText($datos44['obs'], 95)), 0, 'L');
        $pdf->Ln(4);
    }
}

$pdf->SetY(-30);
$pdf->SetDrawColor(230, 230, 230);
$pdf->Line(10, $pdf->GetY() - 1, 206, $pdf->GetY() - 1);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(90, 90, 90);
$pdf->Cell(196, 5, utf8_decode('Gracias por elegirnos. Conserve este comprobante para futuros controles.'), 0, 1, 'C');
$pdf->Cell(196, 4, utf8_decode('Documento generado automaticamente por el sistema de ventas.'), 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->Output("ventas.pdf", "I");

?>

