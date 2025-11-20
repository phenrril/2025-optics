<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];

// Validar conexión primero
if (!$conexion || !is_object($conexion)) {
    echo "<div class='alert alert-danger'>Error de conexión a la base de datos</div>";
    exit();
}

$permiso = "ventas";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}
include_once "includes/header.php";

// Sanitizar ID de usuario
$id_user = (int) $id_user;

// DEBUG: Log para verificar qué usuario está consultando
error_log("Lista ventas - Usuario logueado ID: $id_user - Mostrando TODAS las ventas (sin filtro de usuario)");

// Consulta para contar total de ventas (SIN filtro de usuario - mostrar todas)
// Usar LEFT JOIN para evitar que se pierdan ventas si hay problemas con el cliente
$query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre FROM ventas v LEFT JOIN cliente c ON v.id_cliente = c.idcliente ORDER BY v.fecha DESC, v.id DESC");

// DEBUG: Verificar si hay ventas
if ($query === false) {
    error_log("Error en consulta de ventas: " . mysqli_error($conexion));
} else {
    $num_ventas_total = mysqli_num_rows($query);
    error_log("Total de ventas encontradas (todas): $num_ventas_total");
    
    // DEBUG: Verificar las últimas 5 ventas y sus id_usuario
    $query_debug = mysqli_query($conexion, "SELECT id, id_usuario, fecha, total FROM ventas ORDER BY fecha DESC, id DESC LIMIT 5");
    if ($query_debug) {
        error_log("Últimas 5 ventas en BD:");
        while ($row_debug = mysqli_fetch_assoc($query_debug)) {
            error_log("  - Venta ID: {$row_debug['id']}, Usuario: {$row_debug['id_usuario']}, Fecha: {$row_debug['fecha']}, Total: {$row_debug['total']}");
        }
    }
}
if ($query === false) {
    $error_msg = "Error en consulta de ventas: " . mysqli_error($conexion);
    error_log($error_msg);
    echo "<div class='alert alert-danger'>$error_msg</div>";
    $total_ventas = 0;
} else {
    $total_ventas = mysqli_num_rows($query);
}

// Consulta para total general (SIN filtro de usuario - mostrar todas)
$query_all = mysqli_query($conexion, "SELECT SUM(total) as total_general FROM ventas");
if ($query_all === false) {
    $error_msg = "Error en consulta de total general: " . mysqli_error($conexion);
    error_log($error_msg);
    $total_general = array('total_general' => 0);
} else {
    $total_general = mysqli_fetch_assoc($query_all);
    if (!$total_general || $total_general['total_general'] === null) {
        $total_general = array('total_general' => 0);
    }
}
?>

<style>
/* Estilos modernos para lista de ventas */
.ventas-container {
    max-width: 1400px;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h2 {
    margin: 0;
    font-weight: 600;
    font-size: 2rem;
}

.stats-box {
    background: rgba(255, 255, 255, 0.2);
    padding: 15px 25px;
    border-radius: 10px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.stats-box h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.stats-box p {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
    opacity: 0.9;
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

.card-body-modern {
    padding: 0;
}

.table-modern {
    border-collapse: separate;
    border-spacing: 0;
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 15px;
    border: none;
}

.table-modern tbody tr {
    transition: all 0.3s;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.btn-action-pdf {
    padding: 8px 15px;
    border-radius: 8px;
    border: none;
    transition: all 0.3s;
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
    font-weight: 600;
}

.btn-action-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(235, 51, 73, 0.4);
    color: white;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 20px;
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
    .page-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .stats-box {
        width: 100%;
    }
}
</style>

<div class="ventas-container fade-in-container">
    <!-- Encabezado -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-receipt mr-2"></i> Lista de Ventas</h2>
            <p class="mb-0 mt-2"><i class="fas fa-calendar-alt mr-1"></i> Historial de todas las ventas realizadas</p>
        </div>
        <div>
            <div class="stats-box">
                <h3><i class="fas fa-chart-line mr-2"></i> <?php echo $total_ventas; ?></h3>
                <p>Total de Ventas</p>
            </div>
            <?php if ($total_general['total_general']) { ?>
            <div class="stats-box mt-2">
                <h3><i class="fas fa-dollar-sign mr-2"></i> $<?php echo number_format($total_general['total_general'], 2); ?></h3>
                <p>Total General</p>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card-modern">
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table table-modern" id="tbl">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag mr-1"></i> ID</th>
                            <th><i class="fas fa-user mr-1"></i> Cliente</th>
                            <th><i class="fas fa-dollar-sign mr-1"></i> Total</th>
                            <th><i class="fas fa-calendar mr-1"></i> Fecha</th>
                            <th><i class="fas fa-file-pdf mr-1"></i> Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Consulta para obtener las ventas (reutilizar si es posible)
                        if (isset($query) && $query !== false) {
                            // Reiniciar el puntero del resultado para poder iterarlo de nuevo
                            mysqli_data_seek($query, 0);
                            $query_data = $query;
                        } else {
                            // Si la consulta anterior falló, intentar de nuevo
                            // Usar LEFT JOIN para evitar que se pierdan ventas si hay problemas con el cliente
                            // SIN filtro de usuario - mostrar todas las ventas
                            $query_data = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre FROM ventas v LEFT JOIN cliente c ON v.id_cliente = c.idcliente ORDER BY v.fecha DESC, v.id DESC");
                        }
                        
                        if ($query_data === false) {
                            $error_msg = "Error al obtener ventas: " . mysqli_error($conexion);
                            error_log($error_msg);
                            echo "<tr><td colspan='5' class='alert alert-danger'>$error_msg</td></tr>";
                        } elseif (mysqli_num_rows($query_data) > 0) {
                            while ($row = mysqli_fetch_assoc($query_data)) { 
                                // Formatear fecha
                                $fecha = date('d/m/Y H:i', strtotime($row['fecha']));
                        ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($row['id']); ?></strong></td>
                                <td><i class="fas fa-user-circle text-primary mr-2"></i><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><strong class="text-success">$<?php echo number_format($row['total'], 2); ?></strong></td>
                                <td><i class="far fa-clock text-info mr-1"></i><?php echo $fecha; ?></td>
                                <td>
                                    <a href="pdf/generar.php?cl=<?php echo $row['id_cliente']; ?>&v=<?php echo $row['id']; ?>" 
                                       target="_blank" 
                                       class="btn btn-action-pdf btn-sm"
                                       title="Ver/Descargar PDF">
                                        <i class="fas fa-file-pdf mr-1"></i> Ver PDF
                                    </a>
                                </td>
                            </tr>
                        <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    <h5 class="mt-3 mb-2">No hay ventas registradas</h5>
                                    <p class="text-muted">Las ventas que realices aparecerán aquí</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tbl').DataTable({
            "order": [[3, "desc"]], // Ordenar por fecha (columna 3) descendente
            "language": {
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
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "pageLength": 25, // Mostrar 25 registros por página
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]]
        });
    });
</script>

<?php include_once "includes/footer.php"; ?>