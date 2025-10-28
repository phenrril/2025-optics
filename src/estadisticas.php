<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "estadisticas";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}
include_once "includes/header.php";

$usuarios = mysqli_query($conexion, "SELECT * FROM usuario");
$totalU= mysqli_num_rows($usuarios);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente");
$totalC = mysqli_num_rows($clientes);

$productos = mysqli_query($conexion, "SELECT * FROM producto WHERE existencia > 0");
$totalP = mysqli_num_rows($productos);
$ventas = mysqli_query($conexion, "SELECT * FROM ventas");
$totalV = mysqli_num_rows($ventas);
?>

<style>
/* Estilos modernos para estadísticas */
.estadisticas-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.page-header h2 {
    margin: 0;
    font-weight: 600;
    font-size: 2rem;
}

.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    margin-bottom: 25px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    height: 100%;
    display: block;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    text-decoration: none;
    color: inherit;
}

.stats-card-header {
    padding: 20px 25px;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stats-card-body {
    padding: 25px;
}

.stats-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.stats-card-body {
    position: relative;
}

.stats-icon {
    font-size: 3rem;
    opacity: 0.3;
    position: absolute;
}

.chart-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
    overflow: hidden;
}

.chart-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
}

.chart-body {
    padding: 25px;
    background: #fff;
}

.fade-in-container {
    animation: fadeIn 0.6s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .page-header h2 {
        font-size: 1.5rem;
    }
    
    .stats-value {
        font-size: 2rem;
    }
}
</style>

<div class="estadisticas-container fade-in-container">
    <!-- Encabezado -->
    <div class="page-header">
        <h2><i class="fas fa-chart-line mr-2"></i> Panel de Estadísticas</h2>
        <p class="mb-0 mt-2"><i class="fas fa-info-circle mr-1"></i> Resumen general del sistema</p>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="usuarios.php" class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stats-card-header text-white">
                    <i class="fas fa-user mr-2"></i> Usuarios
                </div>
                <div class="stats-card-body text-white text-center">
                    <div class="stats-value"><?php echo $totalU; ?></div>
                    <p class="mb-0 mt-2 opacity-75"><small>Usuarios registrados</small></p>
                    <div class="stats-icon" style="right: 20px; bottom: 20px;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="clientes.php" class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="stats-card-header text-white">
                    <i class="fas fa-users mr-2"></i> Clientes
                </div>
                <div class="stats-card-body text-white text-center">
                    <div class="stats-value"><?php echo $totalC; ?></div>
                    <p class="mb-0 mt-2 opacity-75"><small>Clientes registrados</small></p>
                    <div class="stats-icon" style="right: 20px; bottom: 20px;">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="productos.php" class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stats-card-header text-white">
                    <i class="fas fa-box mr-2"></i> Productos
                </div>
                <div class="stats-card-body text-white text-center">
                    <div class="stats-value"><?php echo $totalP; ?></div>
                    <p class="mb-0 mt-2 opacity-75"><small>Con stock disponible</small></p>
                    <div class="stats-icon" style="right: 20px; bottom: 20px;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="ventas.php" class="stats-card" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                <div class="stats-card-header text-white">
                    <i class="fas fa-shopping-cart mr-2"></i> Ventas
                </div>
                <div class="stats-card-body text-white text-center">
                    <div class="stats-value"><?php echo $totalV; ?></div>
                    <p class="mb-0 mt-2 opacity-75"><small>Ventas realizadas</small></p>
                    <div class="stats-icon" style="right: 20px; bottom: 20px;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <i class="fas fa-chart-area mr-2"></i> Productos con Stock Mínimo
                </div>
                <div class="chart-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <i class="fas fa-chart-pie mr-2"></i> Productos Más Vendidos
                </div>
                <div class="chart-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; 

$arreglo = array();
    // Solo mostrar productos activos con stock bajo
    $query = mysqli_query($conexion, "SELECT descripcion, existencia FROM producto WHERE existencia <= 10 AND existencia > 0 AND estado = 1 ORDER BY existencia ASC LIMIT 10");
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