<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}

include_once "includes/header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    header("Location: historia_clinica.php");
    exit();
}

// Obtener la historia clínica
$query_historia = "SELECT h.*, c.nombre as nombre_cliente 
                   FROM historia_clinica h 
                   INNER JOIN cliente c ON h.id_cliente = c.idcliente 
                   WHERE h.id = $id";
$result_historia = mysqli_query($conexion, $query_historia);

if (mysqli_num_rows($result_historia) == 0) {
    echo '<div class="alert alert-danger">Historia clínica no encontrada</div>';
    include_once "includes/footer.php";
    exit();
}

$historia = mysqli_fetch_assoc($result_historia);
?>

<style>
.ventas-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
.page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); }
.page-header h2 { margin: 0; font-weight: 600; font-size: 2rem; }
.card-modern { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 25px; overflow: hidden; }
.card-modern:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); }
.card-header-modern { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px; font-weight: 600; font-size: 1.1rem; border: none; }
.card-header-patient { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px 25px; font-weight: 600; font-size: 1.1rem; border: none; }
.card-header-info { background: linear-gradient(135deg, #c2e9fb 0%, #a1c4fd 100%); color: #495057; padding: 20px 25px; font-weight: 600; font-size: 1.1rem; border: none; }
.card-body-modern { padding: 25px; }
.btn-modern { border-radius: 10px; padding: 12px 30px; font-weight: 600; transition: all 0.3s; border: none; font-size: 1rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
.btn-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
.btn-modern-secondary { background: linear-gradient(135deg, #868e96 0%, #495057 100%); color: white; }
.btn-modern-secondary:hover { background: linear-gradient(135deg, #777e86 0%, #40454d 100%); color: white; }
.btn-modern-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: white; }
.btn-modern-danger:hover { background: linear-gradient(135deg, #d42d3f 0%, #e05039 100%); color: white; }
</style>

<div class="ventas-container">
    <div class="page-header">
        <h2><i class="fas fa-file-medical"></i> Historia Clínica #<?php echo $historia['id']; ?></h2>
        <p class="mb-0" style="opacity: 0.9;"><?php echo htmlspecialchars($historia['nombre_cliente']); ?></p>
    </div>
    
    <div class="mb-3">
        <a href="historia_clinica.php" class="btn btn-modern btn-modern-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
        <a href="eliminar_historia.php?id=<?php echo $historia['id']; ?>" class="btn btn-modern btn-modern-danger" onclick="return confirm('¿Eliminar esta historia clínica?');">
            <i class="fas fa-trash"></i> Eliminar
        </a>
    </div>
    
    <!-- Información Básica -->
    <div class="card-modern">
        <div class="card-header-patient">
            <i class="fas fa-user mr-1"></i>
            Información General
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-4">
                    <strong>Cliente:</strong> <?php echo htmlspecialchars($historia['nombre_cliente']); ?>
                </div>
                <div class="col-md-4">
                    <strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($historia['fecha'])); ?>
                </div>
                <div class="col-md-4">
                    <strong>Tipo de Lente:</strong> 
                    <span class="badge badge-primary"><?php echo htmlspecialchars($historia['tipo_lente'] ?: '-'); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graduación -->
    <div class="card-modern">
        <div class="card-header-info">
            <i class="fas fa-eye mr-1"></i>
            Graduación Prescrita
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-eye"></i> Ojo Derecho (OD)</h6>
                    <p class="mb-0">
                        <strong>Esfera:</strong> <?php echo htmlspecialchars($historia['nue_od_esfera'] ?: '-'); ?><br>
                        <strong>Cilindro:</strong> <?php echo htmlspecialchars($historia['nue_od_cilindro'] ?: '-'); ?><br>
                        <strong>Eje:</strong> <?php echo htmlspecialchars($historia['nue_od_eje'] ?: '-'); ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-eye"></i> Ojo Izquierdo (OI)</h6>
                    <p class="mb-0">
                        <strong>Esfera:</strong> <?php echo htmlspecialchars($historia['nue_oi_esfera'] ?: '-'); ?><br>
                        <strong>Cilindro:</strong> <?php echo htmlspecialchars($historia['nue_oi_cilindro'] ?: '-'); ?><br>
                        <strong>Eje:</strong> <?php echo htmlspecialchars($historia['nue_oi_eje'] ?: '-'); ?><br>
                        <?php if ($historia['nue_adicion']) { ?>
                            <strong>Adición:</strong> <?php echo htmlspecialchars($historia['nue_adicion']); ?>
                        <?php } ?>
                    </p>
                </div>
            </div>
            <?php if ($historia['observaciones']) { ?>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-12">
                        <h6><i class="fas fa-sticky-note"></i> Observaciones</h6>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($historia['observaciones'])); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
