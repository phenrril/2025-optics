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

.card-header-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-header-info {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.card-header-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
    font-size: 0.9rem;
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 12px 10px;
    border: none;
}

.table-modern tbody tr {
    transition: all 0.3s;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
}

.table-modern tbody td {
    padding: 12px 10px;
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

.kpi-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 CHF 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    height: 100%;
    border-left: 5px solid;
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.kpi-card.sales {
    border-color: #667eea;
}

.kpi-card.profit {
    border-color: #38ef7d;
}

.kpi-card.transactions {
    border-color: #f5576c;
}

.kpi-card.expenses {
    border-color: #f093fb;
}

.kpi-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 10px 0;
}

.kpi-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.period-selector {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 20px;
}

.period-btn {
    padding: 8px 20px;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s;
}

.period-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.period-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
}

.section-divider {
    margin: 40px 0 20px;
    border-top: 2px solid #e9ecef;
}
</style>

<div class="reporte-container fade-in-container">
    <!-- Encabezado -->
    <div class="page-header text-center">
        <h2><i class="fas fa-chart-bar mr-2"></i> Reporte Financiero</h2>
        <p class="mb-0 mt-2"><i class="fas fa-info-circle mr-1"></i> Análisis completo de ventas, ganancias, ingresos y egresos</p>
    </div>

    <!-- Formulario de Búsqueda -->
    <div class="card card-modern">
        <div class="card-header-modern">
            <i class="fas fa-search mr-2"></i> Seleccionar Período de Análisis
        </div>
        <div class="card-body card-body-modern">
            <form action="" method="GET" id="reportForm">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label><i class="far fa-calendar-check mr-2 text-primary"></i> Fecha Inicial *</label>
                            <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01'); ?>" class="form-control form-control-modern" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label><i class="far fa-calendar-times mr-2 text-danger"></i> Fecha Final *</label>
                            <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d'); ?>" class="form-control form-control-modern" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-modern btn-modern-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Buscar
                        </button>
                </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas KPI y Reporte -->
    <?php if (isset($_GET['from_date']) && isset($_GET['to_date'])) { 
        $from_date = mysqli_real_escape_string($conexion, $_GET['from_date']);
        $to_date = mysqli_real_escape_string($conexion, $_GET['to_date']);
        
        // ============ CALCULAR MÉTRICAS ============
        // Ventas totales
        $query_ventas = mysqli_query($conexion, "SELECT 
            COUNT(DISTINCT v.id) as total_ventas,
            COUNT(dv.id_producto) as total_productos,
            SUM(v.total) as total_ventas_monto
            FROM ventas v
            LEFT JOIN detalle_venta dv ON v.id = dv.id_venta
            WHERE v.fecha BETWEEN '$from_date' AND '$to_date'");
        $ventas_data = mysqli_fetch_assoc($query_ventas);
        $total_transacciones = $ventas_data['total_ventas'] ?? 0;
        $total_items = $ventas_data['total_productos'] ?? 0;
        $total_ventas = $ventas_data['total_ventas_monto'] ?? 0;
        
        // Costos y ganancias
        $query_costs = mysqli_query($conexion, "SELECT 
            SUM(p.precio_bruto * dv.cantidad) as total_costos,
            SUM(p.precio * dv.cantidad) as total_neto
            FROM detalle_venta dv
            JOIN ventas v ON dv.id_venta = v.id
            JOIN producto p ON dv.id_producto = p.codproducto
            WHERE v.fecha BETWEEN '$from_date' AND '$to_date'");
        $costs_data = mysqli_fetch_assoc($query_costs);
        $total_costos = $costs_data['total_costos'] ?? 0;
        $total_neto = $costs_data['total_neto'] ?? 0;
        $ganancia_bruta = $total_neto - $total_costos;
        
        // Ingresos adicionales
        $query_ingresos = mysqli_query($conexion, "SELECT SUM(ingresos) as total FROM ingresos WHERE fecha BETWEEN '$from_date' AND '$to_date'");
        $ingresos_data = mysqli_fetch_assoc($query_ingresos);
        $total_ingresos = $ingresos_data['total'] ?? 0;
        
        // Egresos
        $query_egresos = mysqli_query($conexion, "SELECT SUM(egresos) as total FROM egresos WHERE fecha BETWEEN '$from_date' AND '$to_date'");
        $egresos_data = mysqli_fetch_assoc($query_egresos);
        $total_egresos = $egresos_data['total'] ?? 0;
        
        // Ganancia total
        $ganancia_total = $ganancia_bruta + $total_ingresos - $total_egresos;
        
        // Ticket promedio
        $ticket_promedio = $total_transacciones > 0 ? $total_ventas / $total_transacciones : 0;
        
        // Margen de ganancia
        $margen_ganancia = $total_neto > 0 ? ($ganancia_bruta / $total_neto) * 100 : 0;
    ?>

        <!-- Tarjetas KPI -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="kpi-card sales">
                    <div class="kpi-icon text-primary">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="kpi-label">Ventas Totales</div>
                    <div class="kpi-value text-primary">$<?php echo number_format($total_ventas, 2); ?></div>
                    <div class="text-muted small"><?php echo $total_transacciones; ?> transacciones</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="kpi-card profit">
                    <div class="kpi-icon text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-label">Ganancia Neta</div>
                    <div class="kpi-value text-success">$<?php echo number_format($ganancia_total, 2); ?></div>
                    <div class="text-muted small"><?php echo number_format($margen_ganancia, 1); ?>% margen</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="kpi-card transactions">
                    <div class="kpi-icon text-danger">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="kpi-label">Ticket Promedio</div>
                    <div class="kpi-value text-danger">$<?php echo number_format($ticket_promedio, 2); ?></div>
                    <div class="text-muted small"><?php echo $total_items; ?> productos vendidos</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="kpi-card expenses">
                    <div class="kpi-icon text-warning">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="kpi-label">Egresos</div>
                    <div class="kpi-value text-warning">-$<?php echo number_format($total_egresos, 2); ?></div>
                    <div class="text-muted small">+$<?php echo number_format($total_ingresos, 2); ?> ingresos</div>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="card card-modern mb-4">
            <div class="card-header-modern card-header-purple">
                <i class="fas fa-balance-scale mr-2"></i> Resumen Financiero del Período
            </div>
            <div class="card-body card-body-modern">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-dollar-sign fa-2x text-primary mb-2"></i>
                            <div class="font-weight-bold text-primary" style="font-size: 1.5rem;">$<?php echo number_format($total_ventas, 2); ?></div>
                            <small class="text-muted">VENTAS TOTALES</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-minus-circle fa-2x text-danger mb-2"></i>
                            <div class="font-weight-bold text-danger" style="font-size: 1.5rem;">$<?php echo number_format($total_costos, 2); ?></div>
                            <small class="text-muted">COSTOS</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                            <i class="fas fa-plus-circle fa-2x text-success mb-2"></i>
                            <div class="font-weight-bold text-success" style="font-size: 1.5rem;">$<?php echo number_format($total_ingresos, 2); ?></div>
                            <small class="text-muted">INGRESOS ADICIONALES</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 10px;">
                            <i class="fas fa-calculator fa-2x text-white mb-2"></i>
                            <div class="font-weight-bold text-white" style="font-size: 1.5rem;">$<?php echo number_format($ganancia_total, 2); ?></div>
                            <small class="text-white">GANANCIA FINAL</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de Ventas -->
        <div class="card card-modern mb-4">
            <div class="card-header-modern card-header-purple">
                <i class="fas fa-list-ul mr-2"></i> Detalle de Ventas
        </div>
        <div class="card-body-modern p-0">
            <div class="table-responsive">
                    <table class="table table-modern" id="tablaVentas">
                    <thead>
                        <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                            <th>Cantidad</th>
                                <th>Usuario</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $query_detalle = mysqli_query($conexion, "SELECT 
                            v.id as id_venta,
                            v.fecha,
                            v.total,
                            c.nombre as cliente_nombre,
                            c.telefono as cliente_tel,
                            u.nombre as usuario_nombre,
                            p.descripcion as producto_nombre,
                            dv.cantidad,
                            dv.precio as precio_unitario
                            FROM detalle_venta dv
                            JOIN ventas v ON dv.id_venta = v.id
                            JOIN cliente c ON v.id_cliente = c.idcliente
                            JOIN usuario u ON v.id_usuario = u.idusuario
                            JOIN producto p ON dv.id_producto = p.codproducto
                            WHERE v.fecha BETWEEN '$from_date' AND '$to_date'
                            ORDER BY v.fecha DESC, v.id DESC");
                        
                        if (mysqli_num_rows($query_detalle) > 0) {
                            $contador = 1;
                            foreach ($query_detalle as $row) {
                                ?>
                                <tr>
                                    <td><?php echo $contador++; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['cliente_nombre']); ?></strong><br>
                                        <small class="text-muted"><?php echo $row['cliente_tel']; ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['producto_nombre']); ?></td>
                                    <td class="text-center"><?php echo $row['cantidad']; ?></td>
                                    <td><?php echo htmlspecialchars($row['usuario_nombre']); ?></td>
                                    <td>$<?php echo number_format($row['precio_unitario'], 2); ?></td>
                                    <td><strong>$<?php echo number_format($row['total'], 2); ?></strong></td>
                                </tr>
                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No se encontraron ventas en este período</p>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot class="totals-row">
                            <tr>
                                <td colspan="7"><strong>TOTAL</strong></td>
                                <td><strong>$<?php echo number_format($total_ventas, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ingresos y Egresos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-modern">
                    <div class="card-header-modern card-header-info">
                        <i class="fas fa-arrow-up mr-2"></i> Ingresos Adicionales
                    </div>
                    <div class="card-body-modern p-0">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query_ing = mysqli_query($conexion, "SELECT fecha, ingresos FROM ingresos WHERE fecha BETWEEN '$from_date' AND '$to_date' ORDER BY fecha DESC");
                                if (mysqli_num_rows($query_ing) > 0) {
                                    foreach ($query_ing as $ing) {
                                ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($ing['fecha'])); ?></td>
                                        <td><strong class="text-success">+$<?php echo number_format($ing['ingresos'], 2); ?></strong></td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="2" class="text-center py-4">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            No hay ingresos registrados
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot class="totals-row">
                                    <tr>
                                        <td><strong>TOTAL</strong></td>
                                        <td><strong>$<?php echo number_format($total_ingresos, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-modern">
                    <div class="card-header-modern card-header-warning">
                        <i class="fas fa-arrow-down mr-2"></i> Egresos
                    </div>
                    <div class="card-body-modern p-0">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query_egr = mysqli_query($conexion, "SELECT fecha, egresos FROM egresos WHERE fecha BETWEEN '$from_date' AND '$to_date' ORDER BY fecha DESC");
                                if (mysqli_num_rows($query_egr) > 0) {
                                    foreach ($query_egr as $egr) {
                    ?>
                                <tr>
                                        <td><?php echo date('d/m/Y', strtotime($egr['fecha'])); ?></td>
                                        <td><strong class="text-danger">-$<?php echo number_format($egr['egresos'], 2); ?></strong></td>
                                </tr>
                            <?php
                            }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="2" class="text-center py-4">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            No hay egresos registrados
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="totals-row">
                        <tr>
                                        <td><strong>TOTAL</strong></td>
                                        <td><strong>$<?php echo number_format($total_egresos, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
            </div>
        </div>

    <?php } ?>
</div>

<script>
    $(document).ready(function() {
        $('#tablaVentas').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    });
</script>

<?php include_once "includes/footer.php"; ?>
