<?php include_once "includes/header.php";
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "estadisticas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}

$usuarios = mysqli_query($conexion, "SELECT * FROM usuario");
$totalU= mysqli_num_rows($usuarios);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente");
$totalC = mysqli_num_rows($clientes);
$productos = mysqli_query($conexion, "SELECT * FROM producto");
$totalP = mysqli_num_rows($productos);
$ventas = mysqli_query($conexion, "SELECT * FROM ventas");
$totalV = mysqli_num_rows($ventas);
?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray" style="color: white;">Panel de Estadísticas</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <a class="col-xl-3 col-md-6 mb-4" href="usuarios.php">
            <div class="card border-left-primary shadow h-100 py-2 bg-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Usuarios</div>
                            <div class="h5 mb-0 font-weight-bold text-white"><?php echo $totalU; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Earnings (Monthly) Card Example -->
        <a class="col-xl-3 col-md-6 mb-4" href="clientes.php">
            <div class="card border-left-success shadow h-100 py-2 bg-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Clientes</div>
                            <div class="h5 mb-0 font-weight-bold text-white"><?php echo $totalC; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Earnings (Monthly) Card Example -->
        <a class="col-xl-3 col-md-6 mb-4" href="productos.php">
            <div class="card border-left-info shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Productos</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-white"><?php echo $totalP; ?></div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Pending Requests Card Example -->
        <a class="col-xl-3 col-md-6 mb-4" href="ventas.php">
            <div class="card border-left-warning bg-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Ventas</div>
                            <div class="h5 mb-0 font-weight-bold text-white"><?php echo $totalV; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-white-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <div class="col-lg-6">
            <div class="au-card m-b-30">
                <div class="au-card-inner">
                    <h3 class="title-2 m-b-40" >Productos con stock mínimo</h3><br><br><br><br>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="au-card m-b-30">
                <div class="au-card-inner">
                    <h3 class="title-2 m-b-40">Productos más vendidos</h3><br><br><br><br>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

<?php include_once "includes/footer.php"; 

$arreglo = array();
$query = mysqli_query($conexion, "SELECT descripcion, existencia FROM producto WHERE existencia <= 10 ORDER BY existencia ASC LIMIT 10");
while ($data = mysqli_fetch_array($query)) {
    $arreglo[] = $data;
}

$arreglo1 = array();
$query1 = mysqli_query($conexion, "SELECT p.codproducto, p.descripcion, d.id_producto, d.cantidad, SUM(d.cantidad) as total FROM producto p INNER JOIN detalle_venta d WHERE p.codproducto = d.id_producto group by d.id_producto ORDER BY d.cantidad DESC LIMIT 5");
while ($data1 = mysqli_fetch_array($query1)) {
    $arreglo1[] = $data1;
}

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        defaultFontFamily: 'Poppins',
        labels: [<?php foreach($arreglo as $a) { echo '"' . $a['descripcion'] . '",'; } ?>],
        datasets: [{
        label: 'Existencia',
        data: [<?php foreach($arreglo as $a) { echo $a['existencia'] . ','; } ?>],
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 3,
                                pointStyle: 'circle',
                                pointRadius: 5,
                                pointBorderColor: 'transparent',
                                pointBackgroundColor: 'rgba(220,53,69,1)',
    }],
    },
                    options:{
                                responsive: true,
                                tooltips: {
                                    mode: 'index',
                                    titleFontSize: 12,
                                    titleFontColor: '#000',
                                    bodyFontColor: '#000',
                                    backgroundColor: '#fff',
                                    titleFontFamily: 'Poppins',
                                    bodyFontFamily: 'Poppins',
                                    cornerRadius: 3,
                                    intersect: false,
                                },
                                legend: {
                                    display: true,
                                    labels: {
                                        usePointStyle: true,
                                        fontFamily: 'Poppins',
                                    },
                                },
                                scales: {
                                    xAxes: [{
                                        display: true,
                                        gridLines: {
                                            display: true,
                                            drawBorder: true
                                        },
                                        scaleLabel: {
                                            display: false,
                                            labelString: 'Month'
                                        },
                                        ticks: {
                                            fontFamily: "Poppins"
                                        }
                                    }],
                                    yAxes: [{
                                        display: true,
                                        gridLines: {
                                            display: true,
                                            drawBorder: true
                                        },
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'Cantidad',
                                            fontFamily: "Poppins"
                                        },
                                        ticks: {
                                            fontFamily: "Poppins"
                                        }
                                    }]
                                },
                                title: {
                                    display: false,
                                    text: 'Normal Legend'
                                }
                            }
});
</script>
<script>
    var pieCtx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: [<?php foreach($arreglo1 as $a) { echo '"' . $a['descripcion'] . '",'; } ?>],
            datasets: [{
                label: 'Ventas',
                data: [<?php foreach($arreglo1 as $a) { echo $a['total'] . ','; } ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.9)',
                    'rgba(54, 162, 235, 0.9)',
                    'rgba(255, 206, 86, 0.9)',
                    'rgba(75, 192, 192, 0.9)',
                    'rgba(153, 102, 255, 0.9)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                ],
                borderWidth: 1
            }]
        },
    });
</script>