<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "reporte";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}
include_once "includes/header.php"; 
?>

<style>
/* Estilos modernos para reporte */
.reporte-container {
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

.card-modern {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    margin-bottom: 25px;
    overflow: hidden;
}

.card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.card-header-modern {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
}

.card-body-modern {
    padding: 25px;
}

.form-control-modern {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.btn-modern {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.btn-modern-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-modern-primary:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a4190 100%);
    color: white;
}

.table-modern {
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.85rem;
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 12px 8px;
    border: none;
}

.table-modern tbody tr {
    transition: all 0.3s;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
}

.table-modern tbody td {
    padding: 12px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.totals-row {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
}

.totals-row td {
    padding: 20px 12px;
    border-bottom: none;
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
</style>

<div class="reporte-container fade-in-container">
    <!-- Encabezado -->
    <div class="page-header text-center">
        <h2><i class="fas fa-chart-bar mr-2"></i> Reporte de Ventas</h2>
        <p class="mb-0 mt-2"><i class="fas fa-info-circle mr-1"></i> Análisis detallado de ventas, ingresos y egresos por período</p>
    </div>
    <!-- Formulario de Búsqueda -->
    <div class="card card-modern">
        <div class="card-header-modern">
            <i class="fas fa-search mr-2"></i> Seleccionar Período
        </div>
        <div class="card-body card-body-modern">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="far fa-calendar-check mr-2 text-primary"></i> Desde el Día *</label>
                            <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" class="form-control form-control-modern" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="far fa-calendar-times mr-2 text-danger"></i> Hasta el Día *</label>
                            <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" class="form-control form-control-modern" required>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-modern btn-modern-primary">
                        <i class="fas fa-search mr-2"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Reportes -->
    <?php if (isset($_GET['from_date']) && isset($_GET['to_date'])) { ?>
    <div class="card card-modern">
        <div class="card-header-modern">
            <i class="fas fa-list-ul mr-2"></i> Detalle de Transacciones
        </div>
        <div class="card-body-modern p-0">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>ID Venta</th>
                            <th>ID Producto</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Gno Ingresos</th>
                            <th>Gno Egresos</th>
                            <th>Precio Bruto</th>
                            <th>Precio Neto</th>
                            <th>Total Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $from_date = mysqli_real_escape_string($conexion, $_GET['from_date']);
                        $to_date = mysqli_real_escape_string($conexion, $_GET['to_date']);

                        // Consultas
                        $query = mysqli_query($conexion, "SELECT detalle_venta.id_producto as 'idprod', 
                                            detalle_venta.cantidad as 'cantidad',
                                            detalle_venta.id_venta as 'idventa',
                                            ventas.id, ventas.total, ventas.id_usuario, ventas.fecha,
                                            producto.codproducto as 'id_prod', 
                                            producto.precio_bruto as 'preciobruto', 
                                            producto.precio as 'precioneto' from detalle_venta
                                            join ventas on detalle_venta.id_venta = ventas.id
                                            join producto on detalle_venta.id_producto = producto.codproducto
                                            WHERE ventas.fecha between '$from_date' AND '$to_date'");

                        $query2 = mysqli_query($conexion,"SELECT ingresos.ingresos, ingresos.fecha FROM ingresos
                                            WHERE ingresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $query3 = mysqli_query($conexion,"SELECT egresos.egresos, egresos.fecha FROM egresos
                                            WHERE egresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        // Totales
                        $totalventab = mysqli_query($conexion, "SELECT 
                                            sum(producto.precio_bruto * detalle_venta.cantidad) as 'bruto',
                                            sum(producto.precio * detalle_venta.cantidad) as 'precioneto' 
                                            from detalle_venta
                                            join ventas on detalle_venta.id_venta = ventas.id
                                            join producto on detalle_venta.id_producto = producto.codproducto
                                            WHERE ventas.fecha between '$from_date' AND '$to_date'");

                        $ingtot = mysqli_query($conexion,"SELECT sum(ingresos.ingresos) as ingresos FROM ingresos
                                            WHERE ingresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $egrtot = mysqli_query($conexion,"SELECT sum(egresos.egresos) as egresos FROM egresos
                                            WHERE egresos.fecha BETWEEN '$from_date' AND '$to_date'");

                        $ingresos = mysqli_fetch_assoc($ingtot);
                        $toting = $ingresos['ingresos'] ?? 0;
                        $egresos_data = mysqli_fetch_assoc($egrtot);
                        $totegr = $egresos_data['egresos'] ?? 0;

                        $totalb = mysqli_fetch_assoc($totalventab);
                        $totalventabruta = $totalb['bruto'] ?? 0;
                        $totalventaneta = $totalb['precioneto'] ?? 0;
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
                        } else { ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No se encontraron resultados para este período</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="totals-row">
                        <tr>
                            <td colspan="5"><strong>TOTALES</strong></td>
                            <td><strong>$<?php echo number_format($toting, 2); ?></strong></td>
                            <td><strong>-$<?php echo number_format($totegr, 2); ?></strong></td>
                            <td><strong>$<?php echo number_format($totalventabruta, 2); ?></strong></td>
                            <td><strong>$<?php echo number_format($totalventaneta, 2); ?></strong></td>
                            <td><strong>$<?php echo number_format($ganancia, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php include_once "includes/footer.php"; ?>