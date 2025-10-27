<?php include_once "includes/header.php";
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "calendario";
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
        <h2 align="center">Calendario de Ventas, Ingresos y Egresos</h2>
        <br><br>
        <div class="card">
        <div>
            <style>
                th {
                    font-weight: bold;
                    color: white;
                }
            </style>
            <form method="POST" id="form_saldos" name="form_saldos">
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center"><br>
                        <div class="card">
                            <div class="card-header">
                                Ingresos y Egresos
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="valor" class="form-control" type="number" name="valor" placeholder="Ingresá el valor">
                                </div>
                                
                                <div class="form-group">
                                <td colspan=3>Tipo: </td>
                                        <select id="tipo" name="tipo">
                                        <option value="ingreso">Ingreso</option>
                                        <option value="egreso">Egreso</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <td colspan=3>Descripción:  </td>
                                        <select id="descripcion" name="descripcion">
                                            <option value="ingreso capital">Ingreso de capital</option>
                                            <option value="ingresos varios">ingresos varios</option>
                                            <option value="pago proveedores">pago a proveedores</option>
                                            <option value="pago cristales">pago de cristales</option>
                                            <option value="pago de gastos">pago de gastos</option>
                                            <option value="pago de sueldo">pago de sueldo</option>
                                            <option value="pago de alquiler">pago de alquiler</option>
                                            <option value="pago de luz">pago de luz</option>
                                            <option value="pago de agua">pago de agua</option>
                                            <option value="pago de gas">pago de gas</option>
                                            <option value="pago de internet">pago de internet</option>
                                            <option value="pago de telefono">pago de telefono</option>
                                            <option value="pago de impuestos">pago de impuestos</option>
                                            <option value="pago de seguro">pago de seguro</option>
                                            <option value="pago de publicidad">pago de publicidad</option>
                                            <option value="otros">otros</option>
                                        </select>
                                </div>
                                <div class="row justify-content-center">
                                    <input type="button" class="btn btn-primary" value="Agregar Saldo" id="agregar_saldos" name="agregar_saldos"></input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </form>
            <div id="div_saldos"></div>
            <br>
            <br><br>

            
                
                
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><b> Usuario</b></label>
                            <select name="user" class="form-control">
                                        <option value="1">Nati</option>
                                        <option value="8">Sol</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-6">
                            <label><b></b></label> <br>
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </form>
            <table class="table table-striped" id="table_id">
                <thead>
                    <tr class="bg-dark">
                        <th>ID Venta</th>
                        <th>Nombre Cliente</th>
                        <th>Total</th>
                        <th>Abonó</th>
                        <th>Restan</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conexion = mysqli_connect("localhost", "root", "", "sis_venta");
                    if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
                        $user = $_GET['user'];
                        $from_date = $_GET['from_date'];
                        $to_date = $_GET['to_date'];
                        //$query = "SELECT * FROM ventas WHERE fecha BETWEEN '$from_date' AND '$to_date'";
                        $query = "SELECT ventas.*, cliente.nombre FROM ventas
                        JOIN cliente ON ventas.id_cliente = cliente.idcliente
                        WHERE id_usuario = '$user' and ventas.fecha BETWEEN '$from_date' AND '$to_date'";
                        $query_run = mysqli_query($conexion, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            foreach ($query_run as $fila) {
                    ?>
                                <tr>
                                    <td><?php echo $fila['id']; ?></td>
                                    <td><?php echo $fila['nombre']; ?></td>
                                    <td><?php echo $fila['total']; ?></td>
                                    <td><?php echo $fila['abona']; ?></td>
                                    <td><?php echo $fila['resto']; ?></td>
                                    <td><?php echo $fila['fecha']; ?></td>
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
                </tbody>
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