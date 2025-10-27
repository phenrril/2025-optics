<?php include_once "includes/header.php";
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "reporte";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
} ?>

<head>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous"> 
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
</head>
<br>

<div class="container is-fluid">
    <div class="col-xs-12">
        <h2 align="center">Reporte de Ventas</h2>
        <br><br>
        <div class="card">
        <div>
            <style>
                th {
                    font-weight: bold;
                    color: white;
                }
            </style>
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><b>Del Dia</b></label>
                            <input type="date" name="from_date" value="<?php if (isset($_GET['from_date'])) {
                                                                            echo $_GET['from_date'];
                                                                        } ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><b> Hasta el Dia</b></label>
                            <input type="date" name="to_date" value="<?php if (isset($_GET['to_date'])) {
                                                                            echo $_GET['to_date'];
                                                                        } ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><b></b></label> <br>
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </div>
                <br>
            </form>
            <table class="table table-striped" id="table_id">
                <thead>
                    <tr class="bg-dark">
                        <th>ID USUARIO</th>
                        <th>ID VENTA</th>
                        <th>ID PRODUCTO</th>
                        <th>CANTIDAD</th>
                        <th>FECHA</th>
                        <th>GNO INGRESOS</th>
                        <th>GNO EGRESOS</th>
                        <th>PRECIO BRUTO</th>
                        <th>PRECIO NETO</th>
                        
                        <th>TOTAL VENTA</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //$conexion = mysqli_connect("localhost", "root", "", "sis_venta");
                    if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
                        $from_date = $_GET['from_date'];
                        $to_date = $_GET['to_date'];

                        $query = mysqli_query($conexion, "SELECT    detalle_venta.id_producto as 'idprod', 
                                            detalle_venta.cantidad as 'cantidad',
                                            detalle_venta.id_venta as 'idventa',
                                            ventas.id, ventas.total, ventas.id_usuario, ventas.fecha,
                                            producto.codproducto as 'id_prod', 
                                            producto.precio_bruto as 'preciobruto', 
                                            producto.precio as 'precioneto' from detalle_venta
                                            join ventas on detalle_venta.id_venta = ventas.id
                                            join producto on detalle_venta.id_producto = producto.codproducto
                                            WHERE ventas.fecha between '$from_date' AND '$to_date'");

                        $query2= mysqli_query($conexion,"SELECT    ingresos.ingresos, ingresos.fecha FROM ingresos
                                            WHERE ingresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $query3= mysqli_query($conexion,"SELECT    egresos.egresos, egresos.fecha FROM egresos
                                            WHERE egresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $totalventab =  mysqli_query($conexion, "SELECT  
                                                                        detalle_venta.cantidad as 'cantidad',
                                                                        sum(producto.precio_bruto * cantidad) as 'bruto',
                                                                        detalle_venta.id_venta as 'idventa',
                                                                        ventas.id, ventas.fecha,
                                                                        producto.codproducto as 'id_prod',
                                                                        producto.precio_bruto as 'preciobruto',
                                                                        sum(producto.precio * cantidad) as 'precioneto' from detalle_venta
                                                                        join ventas on detalle_venta.id_venta = ventas.id
                                                                        join producto on detalle_venta.id_producto = producto.codproducto
                                                                        WHERE ventas.fecha between '$from_date' AND '$to_date'");

                        $ingtot= mysqli_query($conexion,"SELECT    sum(ingresos.ingresos) as ingresos, ingresos.fecha FROM ingresos
                                            WHERE ingresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $egrtot= mysqli_query($conexion,"SELECT    sum(egresos.egresos) as egresos, egresos.fecha FROM egresos
                                            WHERE egresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $ingresos = mysqli_fetch_assoc($ingtot);
                        $toting = $ingresos['ingresos'];
                        $egresos = mysqli_fetch_assoc($egrtot);
                        $totegr = $egresos['egresos'];

                        $totalb = mysqli_fetch_assoc($totalventab);
                        $totalventabruta = $totalb['bruto'];
                        $totalventaneta = $totalb['precioneto'];
                        $ganancia = $totalventaneta - $totalventabruta;

                        if (mysqli_num_rows($query) > 0 ) {
                        //if (mysqli_num_rows($query_run) > 0 ) {
                                if(mysqli_num_rows($query3) > 0 ){
                                //if(mysqli_num_rows($query_run3) > 0 ){
                                    foreach ($query3 as $fila2) {
                                //foreach ($query_run3 as $fila2) {
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><?php echo $fila2['fecha']; ?></td>
                                    <td></td>
                                    <td><?php echo $fila2['egresos']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    
                                </tr>
                                <?php
                                }}
                                if(mysqli_num_rows($query2) > 0 ){
                                //if(mysqli_num_rows($query_run2) > 0 ){
                                    foreach ($query2 as $fila1) {
                                //foreach ($query_run2 as $fila1) {
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?php echo $fila1['fecha']; ?></td>
                                        <td><?php echo $fila1['ingresos']; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        
                                    </tr>

                    <?php        }}
                                foreach ($query as $fila) {
                                //foreach ($query_run as $fila) {
                    ?>
                                <tr>
                                    <td><?php echo $fila['id_usuario']; ?></td>
                                    <td><?php echo $fila['idventa']; ?></td>
                                    <td><?php echo $fila['id_prod']; ?></td>
                                    <td><?php echo $fila['cantidad']; ?></td>
                                    <td><?php echo $fila['fecha']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td><?php echo $fila['preciobruto']; ?></td>
                                    <td><?php echo $fila['precioneto']; ?></td>
                                    <td><?php echo $fila['total']; ?></td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td><?php echo "No se encontraron resultados"; ?></td>
                        <?php
                        
                        }
                    }
                        ?>
                        </tr>
                </tbody><td></td><td></td><td></td><td></td><td></td>
                <td><b>Total Ingresos: $<?php if(isset($toting)){ 
                                                echo round($toting, 2);}
                                                else{}
                echo "<br>";
                ?>
                </b></td>
                <td><b>Total Egresos: $<?php if(isset($totegr)){ 
                                                echo round($totegr, 2);}
                                                else{}
                echo "<br>";
                ?>
                </b></td>
                <td><b>Total Venta Bruta: $<?php if(isset($totalventabruta)){ 
                                                echo round($totalventabruta, 2);}
                                                else{}
                echo "<br>";
                ?>
                </b></td>
                <td><b>Total Venta Neta: $<?php if(isset($totalventaneta)){ 
                                                echo round($totalventaneta, 2);}
                                                else{}
                echo "<br>";
                ?>
                </b></td>
                <td><b>Ganancia: $<?php if(isset($ganancia)){ 
                                                echo round($ganancia, 2);}
                                                else{}
                echo "<br>";
                ?>
                </b></td>
            </table>
        </div>

    </div>
</div>
</div>
<script>      
$('#agregar_saldos').click(function () {
    var valor = document.getElementById('valor');
    if(valor.value == "" || valor.value == 0){   
    swal.fire
    ({
        position: 'top-end',
        showConfirmButton: false,
        title: 'Error',
        text: 'El valor no puede ser 0',
        icon: 'error'
    })
}
    else{
    if(confirm('¿Está seguro de agregar el valor? (no se puede cancelar)'))
    {
                {   
                $.ajax({
                        url: "saldos.php",
                        type: "POST",
                        data: $("#form_saldos").serialize(),
                        success: function (resultado){
                        $("#div_saldos").html(resultado);
                }
                });
        }
    }
}
})
            
</script>
<?php include_once "includes/footer.php"; ?>