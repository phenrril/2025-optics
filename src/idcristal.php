<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "idcristal";
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
/* Estilos modernos para Gestión de Operaciones */
.operaciones-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-header-operaciones {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    text-align: center;
}

.page-header-operaciones h2 {
    margin: 0;
    font-weight: 600;
    font-size: 2rem;
}

.section-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.2rem;
    border-radius: 15px 15px 0 0;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.card-modern-operaciones {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    margin-bottom: 30px;
    overflow: hidden;
}

.card-modern-operaciones:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.card-body-modern-operaciones {
    padding: 30px;
}

.form-control-modern-operaciones {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px 18px;
    font-size: 1rem;
    transition: all 0.3s;
    width: 100%;
}

.form-control-modern-operaciones:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.btn-operaciones {
    border-radius: 12px;
    padding: 15px 35px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    cursor: pointer;
}

.btn-operaciones-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-operaciones-primary:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a4190 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-operaciones-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-operaciones-success:hover {
    background: linear-gradient(135deg, #0f8680 0%, #2dd66c 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
}

.btn-operaciones-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
}

.btn-operaciones-danger:hover {
    background: linear-gradient(135deg, #d42d3f 0%, #e05039 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(235, 51, 73, 0.4);
}

.input-group-modern-operaciones {
    position: relative;
    margin-bottom: 15px;
}

.input-group-modern-operaciones label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.results-container {
    margin-top: 20px;
    padding: 20px;
    border-radius: 12px;
    min-height: 100px;
}

.fade-in-section {
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
    .page-header-operaciones h2 {
        font-size: 1.5rem;
    }
    
    .card-body-modern-operaciones {
        padding: 20px;
    }
    
    .btn-operaciones {
        width: 100%;
        margin-bottom: 10px;
    }
}

.section-divider {
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
    margin: 40px auto;
    max-width: 200px;
}
</style>

<div class="operaciones-container fade-in-section">
    <!-- Encabezado -->
    <div class="page-header-operaciones">
        <h2><i class="fas fa-tools mr-3"></i> Gestión de Operaciones</h2>
        <p class="mb-0 mt-2">Administración de ID Cristales, Post Pagos y Anulaciones</p>
    </div>

    <!-- Sección 1: ID Cristales -->
    <div class="card card-modern-operaciones">
        <div class="section-header">
            <i class="fas fa-eye"></i> ID Cristales
        </div>
        <div class="card-body card-body-modern-operaciones">
            <form method="post" id="form_cristal">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-modern-operaciones">
                            <label><i class="fas fa-search mr-2"></i> Buscar ID Venta</label>
                            <input 
                                id="idventa" 
                                name="idventa" 
                                class="form-control form-control-modern-operaciones" 
                                type="number" 
                                placeholder="Ingresá el ID de la venta"
                                required
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-modern-operaciones">
                            <label><i class="fas fa-barcode mr-2"></i> Colocar ID Cristal</label>
                            <input 
                                id="idcristal" 
                                name="idcristal" 
                                class="form-control form-control-modern-operaciones" 
                                type="number" 
                                placeholder="Ingresá el ID de cristales"
                                required
                            >
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn-operaciones btn-operaciones-success" id="guardar_cristal" name="guardar_cristal">
                        <i class="fas fa-save mr-2"></i> Colocar ID Cristal
                    </button>
                </div>
            </form>
            <div id="div_cristal" class="results-container"></div>
        </div>
    </div>

    <div class="section-divider"></div>

    <!-- Sección 2: Post Pagos -->
    <div class="card card-modern-operaciones">
        <div class="section-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-money-check-alt"></i> Post Pagos
        </div>
        <div class="card-body card-body-modern-operaciones">
            <form method="post" id="form_venta">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-modern-operaciones">
                            <label><i class="fas fa-search mr-2"></i> Buscar ID Venta</label>
                            <input 
                                id="idventa_postpago" 
                                name="idventa" 
                                class="form-control form-control-modern-operaciones" 
                                type="number" 
                                placeholder="Ingresá el ID de la venta"
                                required
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-modern-operaciones">
                            <label><i class="fas fa-dollar-sign mr-2"></i> Cantidad a Abonar</label>
                            <input 
                                id="idabona" 
                                name="idabona" 
                                class="form-control form-control-modern-operaciones" 
                                type="number" 
                                step="0.01" 
                                min="0"
                                placeholder="Ingresá el monto"
                                required
                            >
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn-operaciones btn-operaciones-primary" id="buscar_venta" name="buscar_venta">
                        <i class="fas fa-search mr-2"></i> Buscar Venta
                    </button>
                </div>
            </form>
            <div id="div_venta" class="results-container"></div>
        </div>
    </div>

    <div class="section-divider"></div>

    <!-- Sección 3: Anular Venta -->
    <div class="card card-modern-operaciones">
        <div class="section-header" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
            <i class="fas fa-ban"></i> Anular Venta
        </div>
        <div class="card-body card-body-modern-operaciones">
            <form method="post" id="form_anular">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="input-group-modern-operaciones">
                            <label><i class="fas fa-search mr-2"></i> Buscar ID Venta</label>
                            <input 
                                id="idanular" 
                                name="idanular" 
                                class="form-control form-control-modern-operaciones" 
                                type="number" 
                                placeholder="Ingresá el ID de la venta"
                                required
                            >
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn-operaciones btn-operaciones-danger" id="anular_venta" name="anular_venta">
                        <i class="fas fa-times-circle mr-2"></i> Anular Venta
                    </button>
                </div>
            </form>
            <div id="div_anular" class="results-container"></div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>