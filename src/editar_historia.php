<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];

include_once "includes/header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: historia_clinica.php");
    exit();
}

// Obtener la historia clínica
$query_historia = "SELECT * FROM historia_clinica WHERE id = $id";
$result_historia = mysqli_query($conexion, $query_historia);

if (mysqli_num_rows($result_historia) == 0) {
    echo '<div class="alert alert-danger">Historia clínica no encontrada</div>';
    include_once "includes/footer.php";
    exit();
}

$historia = mysqli_fetch_assoc($result_historia);

$alert = "";
$success = "";

// Procesar formulario
if (!empty($_POST)) {
    if (empty($_POST['id_cliente']) || empty($_POST['tipo_consulta'])) {
        $alert = '<div class="alert alert-danger" role="alert">Complete los campos obligatorios</div>';
    } else {
        // Escapar datos
        $id_cliente = intval($_POST['id_cliente']);
        $tipo_consulta = mysqli_real_escape_string($conexion, $_POST['tipo_consulta']);
        $motivo_consulta = mysqli_real_escape_string($conexion, $_POST['motivo_consulta']);
        $antecedentes = mysqli_real_escape_string($conexion, $_POST['antecedentes']);
        $antecedentes_familiares = mysqli_real_escape_string($conexion, $_POST['antecedentes_familiares']);
        $medicamentos_actuales = mysqli_real_escape_string($conexion, $_POST['medicamentos_actuales']);
        $alergias = mysqli_real_escape_string($conexion, $_POST['alergias']);
        
        $av_od_lejos = mysqli_real_escape_string($conexion, $_POST['av_od_lejos']);
        $av_oi_lejos = mysqli_real_escape_string($conexion, $_POST['av_oi_lejos']);
        $av_od_cerca = mysqli_real_escape_string($conexion, $_POST['av_od_cerca']);
        $av_oi_cerca = mysqli_real_escape_string($conexion, $_POST['av_oi_cerca']);
        
        $ant_od_esfera = mysqli_real_escape_string($conexion, $_POST['ant_od_esfera']);
        $ant_od_cilindro = mysqli_real_escape_string($conexion, $_POST['ant_od_cilindro']);
        $ant_od_eje = mysqli_real_escape_string($conexion, $_POST['ant_od_eje']);
        $ant_oi_esfera = mysqli_real_escape_string($conexion, $_POST['ant_oi_esfera']);
        $ant_oi_cilindro = mysqli_real_escape_string($conexion, $_POST['ant_oi_cilindro']);
        $ant_oi_eje = mysqli_real_escape_string($conexion, $_POST['ant_oi_eje']);
        $ant_adicion = mysqli_real_escape_string($conexion, $_POST['ant_adicion']);
        
        $nue_od_esfera = mysqli_real_escape_string($conexion, $_POST['nue_od_esfera']);
        $nue_od_cilindro = mysqli_real_escape_string($conexion, $_POST['nue_od_cilindro']);
        $nue_od_eje = mysqli_real_escape_string($conexion, $_POST['nue_od_eje']);
        $nue_oi_esfera = mysqli_real_escape_string($conexion, $_POST['nue_oi_esfera']);
        $nue_oi_cilindro = mysqli_real_escape_string($conexion, $_POST['nue_oi_cilindro']);
        $nue_oi_eje = mysqli_real_escape_string($conexion, $_POST['nue_oi_eje']);
        $nue_adicion = mysqli_real_escape_string($conexion, $_POST['nue_adicion']);
        
        $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);
        $recomendaciones = mysqli_real_escape_string($conexion, $_POST['recomendaciones']);
        $proximo_control = !empty($_POST['proximo_control']) ? "'" . mysqli_real_escape_string($conexion, $_POST['proximo_control']) . "'" : "NULL";
        $profesional = mysqli_real_escape_string($conexion, $_POST['profesional']);
        
        // Actualizar
        $query = "UPDATE historia_clinica SET
            id_cliente = $id_cliente,
            tipo_consulta = '$tipo_consulta',
            motivo_consulta = '$motivo_consulta',
            antecedentes = '$antecedentes',
            antecedentes_familiares = '$antecedentes_familiares',
            medicamentos_actuales = '$medicamentos_actuales',
            alergias = '$alergias',
            av_od_lejos = '$av_od_lejos',
            av_oi_lejos = '$av_oi_lejos',
            av_od_cerca = '$av_od_cerca',
            av_oi_cerca = '$av_oi_cerca',
            ant_od_esfera = '$ant_od_esfera',
            ant_od_cilindro = '$ant_od_cilindro',
            ant_od_eje = '$ant_od_eje',
            ant_oi_esfera = '$ant_oi_esfera',
            ant_oi_cilindro = '$ant_oi_cilindro',
            ant_oi_eje = '$ant_oi_eje',
            ant_adicion = '$ant_adicion',
            nue_od_esfera = '$nue_od_esfera',
            nue_od_cilindro = '$nue_od_cilindro',
            nue_od_eje = '$nue_od_eje',
            nue_oi_esfera = '$nue_oi_esfera',
            nue_oi_cilindro = '$nue_oi_cilindro',
            nue_oi_eje = '$nue_oi_eje',
            nue_adicion = '$nue_adicion',
            observaciones = '$observaciones',
            recomendaciones = '$recomendaciones',
            proximo_control = $proximo_control,
            profesional = '$profesional'
            WHERE id = $id";
        
        if (mysqli_query($conexion, $query)) {
            $success = '<div class="alert alert-success" role="alert">Historia clínica actualizada correctamente</div>';
            // Reobtener los datos actualizados
            $result_historia = mysqli_query($conexion, $query_historia);
            $historia = mysqli_fetch_assoc($result_historia);
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar: ' . mysqli_error($conexion) . '</div>';
        }
    }
}

// Obtener lista de clientes
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE estado = 1 ORDER BY nombre");
?>

<style>
/* Estilos modernos consistentes */
.ventas-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
.page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); }
.page-header h2 { margin: 0; font-weight: 600; font-size: 2rem; }
.card-modern { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 25px; overflow: hidden; }
.card-modern:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); }
.card-header-modern { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px; font-weight: 600; font-size: 1.1rem; border: none; }
.card-header-section { background: linear-gradient(135deg, #c2e9fb 0%, #a1c4fd 100%); color: #495057; padding: 20px 25px; font-weight: 600; font-size: 1.1rem; border: none; }
.card-body-modern { padding: 25px; }
.form-control-modern, .form-control { border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s; font-size: 0.95rem; }
.form-control-modern:focus, .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); outline: none; }
.select-modern, select.form-control { border: 2px solid #e0e0e0 !important; border-radius: 10px !important; padding: 12px 40px 12px 15px !important; font-size: 1rem !important; background-color: #ffffff !important; min-height: 46px !important; appearance: none; -webkit-appearance: none; -moz-appearance: none; }
.select-modern:focus, select.form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); outline: none; }
.btn-modern { border-radius: 10px; padding: 12px 30px; font-weight: 600; transition: all 0.3s; border: none; font-size: 1rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
.btn-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
.btn-modern-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-modern-primary:hover { background: linear-gradient(135deg, #5568d3 0%, #6a4190 100%); color: white; }
.btn-modern-secondary { background: linear-gradient(135deg, #868e96 0%, #495057 100%); color: white; }
.btn-modern-secondary:hover { background: linear-gradient(135deg, #777e86 0%, #40454d 100%); color: white; }
.section-divider { border-top: 2px solid #e0e0e0; margin: 30px 0; padding-top: 20px; }
h5 { color: #495057; font-weight: 600; margin-top: 10px; }
</style>

<div class="ventas-container">
    <div class="page-header">
        <h2><i class="fas fa-edit"></i> Editar Historia Clínica #<?php echo $id; ?></h2>
        <p class="mb-0" style="opacity: 0.9;">Modificar información de la historia clínica</p>
    </div>
    
    <?php echo $alert; ?>
    <?php echo $success; ?>
    
    <div class="mb-3">
        <a href="historia_clinica.php" class="btn btn-modern btn-modern-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-modern">
        <div class="card-header-modern">
            <i class="fas fa-file-medical mr-1"></i>
            Información de la Consulta
        </div>
        <div class="card-body-modern">
            <form method="POST" action="">
                <!-- Información Básica -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="id_cliente">Cliente *</label>
                        <select class="form-control" name="id_cliente" id="id_cliente" required>
                            <option value="">Seleccione un cliente</option>
                            <?php 
                            mysqli_data_seek($clientes, 0);
                            while ($cliente = mysqli_fetch_assoc($clientes)) { 
                                $selected = ($cliente['idcliente'] == $historia['id_cliente']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $cliente['idcliente']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($cliente['nombre']); ?> - <?php echo htmlspecialchars($cliente['dni']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_consulta">Tipo de Consulta *</label>
                        <select class="form-control" name="tipo_consulta" id="tipo_consulta" required>
                            <option value="">Seleccione...</option>
                            <option value="Nueva" <?php echo ($historia['tipo_consulta'] == 'Nueva') ? 'selected' : ''; ?>>Nueva</option>
                            <option value="Control" <?php echo ($historia['tipo_consulta'] == 'Control') ? 'selected' : ''; ?>>Control</option>
                            <option value="Revisión" <?php echo ($historia['tipo_consulta'] == 'Revisión') ? 'selected' : ''; ?>>Revisión</option>
                            <option value="Urgencia" <?php echo ($historia['tipo_consulta'] == 'Urgencia') ? 'selected' : ''; ?>>Urgencia</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="motivo_consulta">Motivo de Consulta</label>
                        <textarea class="form-control" name="motivo_consulta" rows="2"><?php echo htmlspecialchars($historia['motivo_consulta']); ?></textarea>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="profesional">Profesional</label>
                        <input type="text" class="form-control" name="profesional" value="<?php echo htmlspecialchars($historia['profesional']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="proximo_control">Próximo Control</label>
                        <input type="date" class="form-control" name="proximo_control" value="<?php echo $historia['proximo_control'] ? date("Y-m-d", strtotime($historia['proximo_control'])) : ''; ?>">
                    </div>
                </div>
                
                <!-- El resto del formulario es similar a agregar_historia.php -->
                <!-- Por brevedad, incluyo solo las partes críticas -->
                
                <hr>
                <h5>Antecedentes</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="antecedentes">Antecedentes Personales</label>
                        <textarea class="form-control" name="antecedentes" rows="3"><?php echo htmlspecialchars($historia['antecedentes']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="antecedentes_familiares">Antecedentes Familiares</label>
                        <textarea class="form-control" name="antecedentes_familiares" rows="3"><?php echo htmlspecialchars($historia['antecedentes_familiares']); ?></textarea>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="medicamentos_actuales">Medicamentos Actuales</label>
                        <textarea class="form-control" name="medicamentos_actuales" rows="2"><?php echo htmlspecialchars($historia['medicamentos_actuales']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="alergias">Alergias</label>
                        <textarea class="form-control" name="alergias" rows="2"><?php echo htmlspecialchars($historia['alergias']); ?></textarea>
                    </div>
                </div>
                
                <hr>
                <h5>Examen Visual - Agudeza Visual</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="av_od_lejos">OD Lejos</label>
                        <input type="text" class="form-control" name="av_od_lejos" value="<?php echo htmlspecialchars($historia['av_od_lejos']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="av_oi_lejos">OI Lejos</label>
                        <input type="text" class="form-control" name="av_oi_lejos" value="<?php echo htmlspecialchars($historia['av_oi_lejos']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="av_od_cerca">OD Cerca</label>
                        <input type="text" class="form-control" name="av_od_cerca" value="<?php echo htmlspecialchars($historia['av_od_cerca']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="av_oi_cerca">OI Cerca</label>
                        <input type="text" class="form-control" name="av_oi_cerca" value="<?php echo htmlspecialchars($historia['av_oi_cerca']); ?>">
                    </div>
                </div>
                
                <hr>
                <h5>Anteojos Actuales</h5>
                <div class="row mb-3">
                    <div class="col-md-12"><h6>Ojo Derecho (OD)</h6></div>
                    <div class="col-md-4">
                        <label for="ant_od_esfera">Esfera</label>
                        <input type="text" class="form-control" name="ant_od_esfera" value="<?php echo htmlspecialchars($historia['ant_od_esfera']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="ant_od_cilindro">Cilindro</label>
                        <input type="text" class="form-control" name="ant_od_cilindro" value="<?php echo htmlspecialchars($historia['ant_od_cilindro']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="ant_od_eje">Eje</label>
                        <input type="text" class="form-control" name="ant_od_eje" value="<?php echo htmlspecialchars($historia['ant_od_eje']); ?>">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12"><h6>Ojo Izquierdo (OI)</h6></div>
                    <div class="col-md-3">
                        <label for="ant_oi_esfera">Esfera</label>
                        <input type="text" class="form-control" name="ant_oi_esfera" value="<?php echo htmlspecialchars($historia['ant_oi_esfera']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="ant_oi_cilindro">Cilindro</label>
                        <input type="text" class="form-control" name="ant_oi_cilindro" value="<?php echo htmlspecialchars($historia['ant_oi_cilindro']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="ant_oi_eje">Eje</label>
                        <input type="text" class="form-control" name="ant_oi_eje" value="<?php echo htmlspecialchars($historia['ant_oi_eje']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="ant_adicion">Adición</label>
                        <input type="text" class="form-control" name="ant_adicion" value="<?php echo htmlspecialchars($historia['ant_adicion']); ?>">
                    </div>
                </div>
                
                <hr>
                <h5>Nueva Prescripción</h5>
                <div class="row mb-3">
                    <div class="col-md-12"><h6>Ojo Derecho (OD)</h6></div>
                    <div class="col-md-4">
                        <label for="nue_od_esfera">Esfera</label>
                        <input type="text" class="form-control" name="nue_od_esfera" value="<?php echo htmlspecialchars($historia['nue_od_esfera']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="nue_od_cilindro">Cilindro</label>
                        <input type="text" class="form-control" name="nue_od_cilindro" value="<?php echo htmlspecialchars($historia['nue_od_cilindro']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="nue_od_eje">Eje</label>
                        <input type="text" class="form-control" name="nue_od_eje" value="<?php echo htmlspecialchars($historia['nue_od_eje']); ?>">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12"><h6>Ojo Izquierdo (OI)</h6></div>
                    <div class="col-md-3">
                        <label for="nue_oi_esfera">Esfera</label>
                        <input type="text" class="form-control" name="nue_oi_esfera" value="<?php echo htmlspecialchars($historia['nue_oi_esfera']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="nue_oi_cilindro">Cilindro</label>
                        <input type="text" class="form-control" name="nue_oi_cilindro" value="<?php echo htmlspecialchars($historia['nue_oi_cilindro']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="nue_oi_eje">Eje</label>
                        <input type="text" class="form-control" name="nue_oi_eje" value="<?php echo htmlspecialchars($historia['nue_oi_eje']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="nue_adicion">Adición</label>
                        <input type="text" class="form-control" name="nue_adicion" value="<?php echo htmlspecialchars($historia['nue_adicion']); ?>">
                    </div>
                </div>
                
                <hr>
                <h5>Observaciones y Recomendaciones</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3"><?php echo htmlspecialchars($historia['observaciones']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="recomendaciones">Recomendaciones</label>
                        <textarea class="form-control" name="recomendaciones" rows="3"><?php echo htmlspecialchars($historia['recomendaciones']); ?></textarea>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-modern btn-modern-primary">
                            <i class="fas fa-save"></i> Actualizar Historia Clínica
                        </button>
                        <a href="historia_clinica.php" class="btn btn-modern btn-modern-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

