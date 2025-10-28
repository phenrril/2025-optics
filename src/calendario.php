<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "calendario";
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
/* Estilos modernos para calendario */
.calendario-container {
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

.select-modern {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 0.95rem;
    width: 100%;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: #fff;
}

.select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.table-modern {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 12px;
    overflow: hidden;
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
}

.table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
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

<div class="calendario-container fade-in-container">
    <!-- Encabezado -->
    <div class="page-header text-center">
        <h2><i class="fas fa-calendar-alt mr-2"></i> Calendario de Ventas, Ingresos y Egresos</h2>
        <p class="mb-0 mt-2"><i class="fas fa-info-circle mr-1"></i> Gestión de movimientos financieros y ventas por período</p>
    </div>
    <!-- Formulario Ingresos y Egresos -->
    <div class="card card-modern">
        <div class="card-header-modern">
            <i class="fas fa-money-check-alt mr-2"></i> Ingresos y Egresos
        </div>
        <div class="card-body card-body-modern">
            <form method="POST" id="form_saldos" name="form_saldos">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign mr-2 text-success"></i> Valor *</label>
                            <input id="valor" name="valor" class="form-control form-control-modern" type="number" step="0.01" min="0" placeholder="Ingresá el valor" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-exchange-alt mr-2 text-primary"></i> Tipo *</label>
                            <select id="tipo" name="tipo" class="form-control select-modern" required>
                                <option value="ingreso">Ingreso</option>
                                <option value="egreso">Egreso</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-alt mr-2 text-info"></i> Descripción *</label>
                    <select id="descripcion" name="descripcion" class="form-control select-modern" required>
                        <option value="ingreso capital">Ingreso de capital</option>
                        <option value="ingresos varios">Ingresos varios</option>
                        <option value="pago proveedores">Pago a proveedores</option>
                        <option value="pago cristales">Pago de cristales</option>
                        <option value="pago de gastos">Pago de gastos</option>
                        <option value="pago de sueldo">Pago de sueldo</option>
                        <option value="pago de alquiler">Pago de alquiler</option>
                        <option value="pago de luz">Pago de luz</option>
                        <option value="pago de agua">Pago de agua</option>
                        <option value="pago de gas">Pago de gas</option>
                        <option value="pago de internet">Pago de internet</option>
                        <option value="pago de telefono">Pago de teléfono</option>
                        <option value="pago de impuestos">Pago de impuestos</option>
                        <option value="pago de seguro">Pago de seguro</option>
                        <option value="pago de publicidad">Pago de publicidad</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-modern btn-modern-primary" id="agregar_saldos" name="agregar_saldos">
                        <i class="fas fa-plus mr-2"></i> Agregar Saldo
                    </button>
                </div>
            </form>
            <div id="div_saldos" class="mt-3"></div>
        </div>
    </div>

    <!-- Formulario de Búsqueda -->
    <div class="card card-modern">
        <div class="card-header-modern">
            <i class="fas fa-search mr-2"></i> Buscar Ventas por Período
        </div>
        <div class="card-body card-body-modern">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="far fa-calendar-check mr-2 text-primary"></i>Desde el Día *</label>
                            <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" class="form-control form-control-modern" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="far fa-calendar-times mr-2 text-danger"></i>Hasta el Día *</label>
                            <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" class="form-control form-control-modern" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-user mr-2 text-info"></i> Usuario *</label>
                            <select name="user" class="form-control select-modern" required>
                                <option value="1">Nati</option>
                                <option value="8">Sol</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-modern btn-modern-primary">
                        <i class="fas fa-search mr-2"></i> Buscar Ventas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados de Búsqueda -->
    <?php if (isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date'])) { 
        // Validar que la conexión esté disponible
        if (!isset($conexion) || is_null($conexion)) {
            die("Error: La conexión a la base de datos no está disponible.");
        }
        
        $user = mysqli_real_escape_string($conexion, $_GET['user']);
        $from_date = mysqli_real_escape_string($conexion, $_GET['from_date']);
        $to_date = mysqli_real_escape_string($conexion, $_GET['to_date']);
        
        $query = "SELECT ventas.*, cliente.nombre FROM ventas
                  JOIN cliente ON ventas.id_cliente = cliente.idcliente
                  WHERE ventas.id_usuario = '$user' AND ventas.fecha BETWEEN '$from_date' AND '$to_date'";
        $query_run = mysqli_query($conexion, $query);
        
        if (!$query_run) {
            die("Error en la consulta: " . mysqli_error($conexion));
        }
    ?>
        <div class="card card-modern">
            <div class="card-header-modern">
                <i class="fas fa-receipt mr-2"></i> Resultados de Búsqueda
            </div>
            <div class="card-body-modern p-0">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag mr-1"></i> ID Venta</th>
                                <th><i class="fas fa-user mr-1"></i> Nombre Cliente</th>
                                <th><i class="fas fa-dollar-sign mr-1"></i> Total</th>
                                <th><i class="fas fa-money-bill-wave mr-1"></i> Abonó</th>
                                <th><i class="fas fa-balance-scale mr-1"></i> Restante</th>
                                <th><i class="fas fa-calendar mr-1"></i> Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query_run) > 0) {
                                foreach ($query_run as $fila) { ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($fila['id']); ?></strong></td>
                                        <td><i class="fas fa-user-circle text-primary mr-2"></i><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                        <td><strong class="text-success">$<?php echo number_format($fila['total'], 2); ?></strong></td>
                                        <td><i class="fas fa-check-circle text-success mr-1"></i>$<?php echo number_format($fila['abona'], 2); ?></td>
                                        <td><i class="fas fa-clock text-warning mr-1"></i>$<?php echo number_format($fila['resto'], 2); ?></td>
                                        <td><i class="far fa-calendar text-info mr-1"></i><?php echo date('d/m/Y', strtotime($fila['fecha'])); ?></td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No se encontraron resultados para este período</p>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
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