<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "nueva_venta";
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
/* Estilos modernos para ventas */
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
}

.card-body-modern {
    padding: 25px;
}

.form-group-modern {
    margin-bottom: 20px;
}

.form-group-modern label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-control-modern {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px 15px;
    transition: all 0.3s;
    font-size: 0.95rem;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.form-control-modern:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
    opacity: 0.7;
}

.input-group-modern {
    margin-bottom: 15px;
}

.input-group-text-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    min-width: 90px;
    justify-content: center;
}

.input-group-modern .form-control {
    border: 2px solid #e0e0e0;
    border-radius: 0;
    padding: 10px 12px;
}

.input-group-modern .form-control:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.input-group-modern .form-control:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}

.btn-modern {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    font-size: 1rem;
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

.btn-modern-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-modern-success:hover {
    background: linear-gradient(135deg, #0f8680 0%, #2dd66c 100%);
    color: white;
}

.btn-modern-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
}

.btn-modern-danger:hover {
    background: linear-gradient(135deg, #d42d3f 0%, #e05039 100%);
    color: white;
}

.btn-modern-secondary {
    background: linear-gradient(135deg, #868e96 0%, #495057 100%);
    color: white;
}

.btn-modern-secondary:hover {
    background: linear-gradient(135deg, #777e86 0%, #40454d 100%);
    color: white;
}

.select-modern {
    border: 2px solid #e0e0e0 !important;
    border-radius: 10px !important;
    padding: 12px 40px 12px 15px !important;
    font-size: 1rem !important;
    background-color: #ffffff !important;
    color: #333333 !important;
    width: 100% !important;
    height: auto !important;
    min-height: 46px !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    font-weight: 500 !important;
    line-height: 1.5 !important;
    text-align: left !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px 12px;
    box-sizing: border-box !important;
}

.select-modern option {
    padding: 10px !important;
    background-color: white !important;
    color: #333333 !important;
}

.select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

/* Estilo para campo deshabilitado */
.select-modern:disabled {
    background-color: #f8f9fa !important;
    cursor: not-allowed !important;
    opacity: 0.7 !important;
}

.vendor-info {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(245, 87, 108, 0.3);
}

.vendor-info h5 {
    font-weight: 600;
    margin: 0 0 10px 0;
}

.vendor-info .vendor-name {
    font-size: 1.3rem;
    font-weight: 700;
    text-transform: uppercase;
}

.graduation-section {
    background: linear-gradient(135deg, #c2e9fb 0%, #a1c4fd 100%);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.graduation-label {
    font-weight: 700;
    color: #495057;
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.product-search {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(253, 203, 110, 0.3);
}

.search-input {
    border: 3px solid white;
    border-radius: 12px;
    padding: 15px 20px;
    font-size: 1rem;
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    outline: none;
}

.table-modern {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table-modern thead th {
    border: none;
    padding: 15px;
    font-weight: 600;
    font-size: 0.95rem;
}

.table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    transition: all 0.3s;
}

.tfoot-modern {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

.payment-method {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    align-items: center;
}

.payment-option {
    display: inline-flex;
    align-items: center;
    padding: 15px 25px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    flex: 1;
    min-width: 160px;
    max-width: 220px;
    justify-content: center;
}

.payment-option:hover {
    border-color: #667eea;
    background-color: #f0f0ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
}

.payment-option input[type="radio"] {
    margin-right: 8px;
    cursor: pointer;
}

.payment-option input[type="radio"]:checked + label {
    color: #667eea;
    font-weight: 600;
}

.payment-option label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
}

.total-display {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
    margin-bottom: 20px;
}

.total-display h4 {
    font-weight: 600;
    margin: 0 0 10px 0;
}

.total-amount {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    flex-wrap: wrap;
}

.section-title {
    font-weight: 700;
    color: #495057;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid #667eea;
}

.badge-modern {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .page-header h2 {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
    }
}

.fade-in {
    animation: fadeIn 0.5s;
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

.success-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(17, 153, 142, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(17, 153, 142, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(17, 153, 142, 0);
    }
}
</style>

<div class="ventas-container fade-in">
    <!-- Encabezado -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-shopping-cart mr-3"></i> Nueva Venta</h2>
                <p class="mb-0 mt-2"><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?></p>
            </div>
            <div class="vendor-info">
                <h5><i class="fas fa-user-tie mr-2"></i> Vendedor</h5>
                <p class="vendor-name mb-0"><?php echo $_SESSION['nombre']; ?></p>
            </div>
        </div>
    </div>

    <!-- Datos del Cliente -->
    <div class="card card-modern">
        <div class="card-header card-header-modern d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user mr-2"></i> Información del Cliente
            </div>
            <button type="button" class="btn btn-modern btn-modern-success btn-sm" data-toggle="modal" data-target="#nuevo_cliente_venta">
                <i class="fas fa-plus mr-1"></i> Nuevo Cliente
            </button>
        </div>
        <div class="card-body card-body-modern">
                <form method="post">
                    <div class="row">
                    <div class="col-md-3">
                        <input type="hidden" id="idcliente" value="1" name="idcliente">
                        <div class="form-group form-group-modern">
                            <label><i class="fas fa-user mr-2"></i> Nombre del Cliente</label>
                            <input type="text" name="nom_cliente" id="nom_cliente" class="form-control form-control-modern" placeholder="Buscar o ingresar nombre" required>
                        </div>
                            </div>
                    <div class="col-md-3">
                        <div class="form-group form-group-modern">
                            <label><i class="fas fa-phone mr-2"></i> Teléfono</label>
                            <input type="number" name="tel_cliente" id="tel_cliente" class="form-control form-control-modern" disabled>
                        </div>
                            </div>
                    <div class="col-md-3">
                        <div class="form-group form-group-modern">
                            <label><i class="fas fa-map-marker-alt mr-2"></i> Dirección</label>
                            <input type="text" name="dir_cliente" id="dir_cliente" class="form-control form-control-modern" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group form-group-modern">
                            <label><i class="fas fa-hospital mr-2"></i> Obra Social</label>
                            <input type="text" name="obrasocial" id="obrasocial" class="form-control form-control-modern" disabled>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <!-- Datos de la Venta -->
                <div class="row">
        <!-- Columna Izquierda: Graduaciones y Búsqueda de Productos -->
                    <div class="col-lg-6">
            <!-- Graduaciones -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <i class="fas fa-eye mr-2"></i> Graduaciones
                </div>
                <div class="card-body card-body-modern">
                            <form id="graduaciones">
                        <label class="graduation-label">
                            <i class="fas fa-glasses mr-2"></i> Graduación Lejos &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Graduación Cerca
                        </label>
                        
                        <div class="input-group input-group-modern mb-3">    
                            <span class="input-group-text input-group-text-modern">Ojo DL</span>
                            <input type="text" id="ojoDl1" name="ojoDl1" class="form-control" placeholder="Esf">
                            <input type="text" id="ojoDl2" name="ojoDl2" class="form-control" placeholder="Cil">
                            <input type="text" id="ojoDl3" name="ojoDl3" class="form-control" placeholder="Eje">
                            <span class="input-group-text input-group-text-modern">Ojo DC</span>
                            <input type="text" name="ojoD1" id="ojoD1" class="form-control" placeholder="Esf">
                            <input type="text" name="ojoD2" id="ojoD2" class="form-control" placeholder="Cil">
                            <input type="text" name="ojoD3" id="ojoD3" class="form-control" placeholder="Eje">
                        </div>

                        <div class="input-group input-group-modern mb-3">
                            <span class="input-group-text input-group-text-modern">Ojo Iz L</span>
                            <input type="text" id="ojoIl1" name="ojoIl1" class="form-control" placeholder="Esf">
                            <input type="text" id="ojoIl2" name="ojoIl2" class="form-control" placeholder="Cil">
                            <input type="text" id="ojoIl3" name="ojoIl3" class="form-control" placeholder="Eje">
                            <span class="input-group-text input-group-text-modern">Ojo Iz C</span>
                            <input type="text" id="ojoI1" name="ojoI1" class="form-control" placeholder="Esf">
                            <input type="text" id="ojoI2" name="ojoI2" class="form-control" placeholder="Cil">
                            <input type="text" id="ojoI3" name="ojoI3" class="form-control" placeholder="Eje">
                    </div>
                    
                        <div class="input-group input-group-modern mb-4">
                            <span class="input-group-text input-group-text-modern">ADD:</span>
                            <input type="text" id="add" name="add" class="form-control" placeholder="Adición">
                            <span class="input-group-text input-group-text-modern">Obs:</span>
                            <input type="text" id="obs" name="obs" class="form-control" placeholder="Observaciones">
                            </div>

                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-modern btn-modern-primary" id="grad" type="button">
                                <i class="fas fa-plus mr-2"></i> Agregar Graduaciones
                            </button>
                            <button class="btn btn-modern btn-modern-danger" id="borrar_grad" type="button">
                                <i class="fas fa-trash mr-2"></i> Borrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Búsqueda de Productos -->
            <div class="card card-modern product-search">
                <div class="card-body card-body-modern">
                    <h5 class="section-title"><i class="fas fa-search mr-2"></i> Buscar Producto</h5>
                    <div class="form-group">
                        <input id="producto" class="form-control search-input" type="text" name="producto" placeholder="🔍 Ingresá el código o nombre del producto...">
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle mr-1"></i> Escribe mínimo 3 caracteres para buscar
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Detalles de la Venta -->
        <div class="col-lg-6">
        <div id="okgrad"></div>
            
            <!-- Resumen de Venta -->
        <div class="table-responsive">
                <table class="table table-modern" id="tblDetalle">
                    <thead>
                        <tr>
                            <th><i class="fas fa-barcode mr-1"></i> ID</th>
                            <th><i class="fas fa-box mr-1"></i> Descripción</th>
                            <th><i class="fas fa-sort-numeric-up mr-1"></i> Cant.</th>
                            <th><i class="fas fa-dollar-sign mr-1"></i> Precio</th>
                            <th><i class="fas fa-calculator mr-1"></i> Total</th>
                            <th><i class="fas fa-cog mr-1"></i> Acción</th>
                    </tr>
                </thead>
                <tbody id="detalle_venta">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay productos en el carrito</p>
                            </td>
                        </tr>
                </tbody>
                    <tfoot class="tfoot-modern">
                        <tr>
                            <td colspan="4" class="text-right">Total a Pagar:</td>
                            <td colspan="2"><strong>$0.00</strong></td>
                    </tr>    
                </tfoot>
            </table>
            </div>
        </div>
    </div>

    <!-- Totales y Métodos de Pago -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <i class="fas fa-money-bill-wave mr-2"></i> Método de Pago
                </div>
                <div class="card-body card-body-modern">
                    <form method="POST" id="metodo_pago">
                        <div class="payment-method">
                            <div class="payment-option">
                                <input type="radio" id="pago1" name="pago" value="1" checked>
                                <label for="pago1"><i class="fas fa-money-bill-wave mr-2"></i> Efectivo</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="pago2" name="pago" value="2">
                                <label for="pago2"><i class="fas fa-credit-card mr-2"></i> Crédito</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="pago3" name="pago" value="3">
                                <label for="pago3"><i class="fas fa-id-card mr-2"></i> Débito</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="pago4" name="pago" value="4">
                                <label for="pago4"><i class="fas fa-exchange-alt mr-2"></i> Transferencia</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <i class="fas fa-calculator mr-2"></i> Resumen de Pago
                </div>
                <div class="card-body card-body-modern">
                <form method="POST" id="form_descuento">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold"><i class="fas fa-hand-holding-usd mr-2"></i> Abona</label>
                                <input type="number" class="form-control form-control-modern select-modern" id="abona" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold"><i class="fas fa-percentage mr-2"></i> % Descuento</label>
                                <select id="porc" name="porc" class="form-control form-control-modern select-modern" style="width: 100%; display: block;">
                                    <option value="1">0% - Sin descuento</option>
                                        <option value="0.95">5%</option>
                                        <option value="0.9">10%</option>
                                        <option value="0.85">15%</option>
                                        <option value="0.80">20%</option>
                                        <option value="0.75">25%</option>
                                        <option value="0.70">30%</option>
                                        <option value="0.65">35%</option>
                                        <option value="0.60">40%</option>
                                        <option value="0.55">45%</option>
                                        <option value="0.50">50%</option>
                                        <option value="0.45">55%</option>
                                        <option value="0.40">60%</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold"><i class="fas fa-heartbeat mr-2"></i> Obra Social</label>
                                <input type="number" class="form-control form-control-modern select-modern" id="obra_social" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold"><i class="fas fa-balance-scale mr-2"></i> Resta</label>
                                <input type="number" class="form-control form-control-modern select-modern" id="resto" disabled placeholder="0.00">
                            </div>
                        </div>
                        <input type="number" id="total" hidden disabled>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Display -->
    <div class="total-display" id="total-display">
        <h4><i class="fas fa-receipt mr-2"></i> Total de la Venta</h4>
        <p class="total-amount" id="total-amount">$0.00</p>
    </div>

    <!-- Botones de Acción -->
    <div class="action-buttons">
        <button class="btn btn-modern btn-modern-success btn-lg" id="btn_generar">
            <i class="fas fa-save mr-2"></i> Generar Venta
        </button>
        <button class="btn btn-modern btn-modern-secondary btn-lg" id="btn_parcial">
            <i class="fas fa-calculator mr-2"></i> Simular Venta
        </button>
    </div>
</div>

<!-- Modal para agregar nuevo cliente -->
<div id="nuevo_cliente_venta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-nuevo-cliente" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white" id="modal-nuevo-cliente">
                    <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_nuevo_cliente">
                    <div class="form-group">
                        <label for="nombre_cliente"><i class="fas fa-user mr-2"></i> Nombre *</label>
                        <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" placeholder="Ingrese Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono_cliente"><i class="fas fa-phone mr-2"></i> Teléfono *</label>
                        <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente" placeholder="Ingrese Teléfono" required>
                    </div>
                    <div class="form-group">
                        <label for="direccion_cliente"><i class="fas fa-map-marker-alt mr-2"></i> Dirección *</label>
                        <input type="text" class="form-control" id="direccion_cliente" name="direccion_cliente" placeholder="Ingrese Dirección" required>
                    </div>
                    <div class="form-group">
                        <label for="dni_cliente"><i class="fas fa-id-card mr-2"></i> DNI</label>
                        <input type="text" class="form-control" id="dni_cliente" name="dni_cliente" placeholder="Ingrese DNI">
                    </div>
                    <div class="form-group">
                        <label for="obrasocial_cliente"><i class="fas fa-hospital mr-2"></i> Obra Social</label>
                        <input type="text" class="form-control" id="obrasocial_cliente" name="obrasocial_cliente" placeholder="Ingrese Obra Social">
                    </div>
                    <div class="form-group">
                        <label for="medico_cliente"><i class="fas fa-user-md mr-2"></i> Médico</label>
                        <input type="text" class="form-control" id="medico_cliente" name="medico_cliente" placeholder="Ingrese Médico">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </button>
                <button type="button" class="btn btn-modern btn-modern-primary" id="btn_guardar_cliente" onclick="guardarNuevoCliente()">
                    <i class="fas fa-save mr-2"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
