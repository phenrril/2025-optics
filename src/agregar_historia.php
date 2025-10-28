<?php 
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "historia_clinica";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}

include_once "includes/header.php";

$alert = "";
$success = "";

// Procesar formulario
if (!empty($_POST)) {
    if (empty($_POST['id_cliente']) || empty($_POST['tipo_consulta'])) {
        $alert = '<div class="alert alert-danger" role="alert">Complete los campos obligatorios</div>';
    } else {
        $id_cliente = intval($_POST['id_cliente']);
        $tipo_consulta = mysqli_real_escape_string($conexion, $_POST['tipo_consulta']);
        $tipo_lente = mysqli_real_escape_string($conexion, $_POST['tipo_lente']);
        
        // Graduaciones
        $nue_od_esfera = mysqli_real_escape_string($conexion, $_POST['nue_od_esfera']);
        $nue_od_cilindro = mysqli_real_escape_string($conexion, $_POST['nue_od_cilindro']);
        $nue_od_eje = mysqli_real_escape_string($conexion, $_POST['nue_od_eje']);
        $nue_oi_esfera = mysqli_real_escape_string($conexion, $_POST['nue_oi_esfera']);
        $nue_oi_cilindro = mysqli_real_escape_string($conexion, $_POST['nue_oi_cilindro']);
        $nue_oi_eje = mysqli_real_escape_string($conexion, $_POST['nue_oi_eje']);
        $nue_adicion = mysqli_real_escape_string($conexion, $_POST['nue_adicion']);
        
        $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);
        
        // Insertar
        $query = "INSERT INTO historia_clinica (
            id_cliente, tipo_consulta, tipo_lente,
            nue_od_esfera, nue_od_cilindro, nue_od_eje,
            nue_oi_esfera, nue_oi_cilindro, nue_oi_eje, nue_adicion,
            observaciones, id_usuario
        ) VALUES (
            $id_cliente, '$tipo_consulta', '$tipo_lente',
            '$nue_od_esfera', '$nue_od_cilindro', '$nue_od_eje',
            '$nue_oi_esfera', '$nue_oi_cilindro', '$nue_oi_eje', '$nue_adicion',
            '$observaciones', $id_user
        )";
        
        if (mysqli_query($conexion, $query)) {
            $success = '<div class="alert alert-success" role="alert">Historia clínica registrada correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al registrar: ' . mysqli_error($conexion) . '</div>';
        }
    }
}

$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE estado = 1 ORDER BY nombre");
?>

<style>
.ventas-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
.page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); }
.page-header h2 { margin: 0; font-weight: 600; font-size: 2rem; }
.card-modern { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 25px; overflow: hidden; }
.card-modern:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); }
.card-header-modern { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px; font-weight: 600; font-size: 1.1rem; border: none; }
.card-body-modern { padding: 25px; }
.form-control-modern, .form-control { border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; transition: all 0.3s; font-size: 0.95rem; }
.form-control-modern:focus, .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); outline: none; }
select.form-control { 
    height: auto !important; 
    line-height: 1.8 !important; 
    padding-top: 10px !important; 
    padding-bottom: 10px !important;
    padding-left: 12px !important;
    padding-right: 40px !important;
}
.btn-modern { border-radius: 10px; padding: 12px 30px; font-weight: 600; transition: all 0.3s; border: none; font-size: 1rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
.btn-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
.btn-modern-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-modern-primary:hover { background: linear-gradient(135deg, #5568d3 0%, #6a4190 100%); color: white; }
.btn-modern-secondary { background: linear-gradient(135deg, #868e96 0%, #495057 100%); color: white; }
.btn-modern-secondary:hover { background: linear-gradient(135deg, #777e86 0%, #40454d 100%); color: white; }
</style>

<div class="ventas-container">
    <div class="page-header">
        <h2><i class="fas fa-file-medical"></i> Nueva Historia Clínica</h2>
        <p class="mb-0" style="opacity: 0.9;">Registrar graduaciones y tipo de lente</p>
    </div>
    
    <?php echo $alert; ?>
    <?php echo $success; ?>
    
    <div class="card-modern">
        <div class="card-header-modern">
            <i class="fas fa-file-medical mr-1"></i>
            Nueva Graduación
        </div>
        <div class="card-body-modern">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cliente_search">Buscar Cliente *</label>
                        <input type="text" class="form-control" id="cliente_search" placeholder="Buscar por nombre o DNI..." autocomplete="off" required>
                        <input type="hidden" name="id_cliente" id="id_cliente" required>
                        <small class="form-text text-muted">Escriba el nombre o DNI del cliente para buscar</small>
                        <div id="resultados-cliente" style="max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-radius: 5px; display: none; position: absolute; z-index: 999; width: 100%;"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_consulta">Tipo de Consulta *</label>
                        <select class="form-control" name="tipo_consulta" id="tipo_consulta" required>
                            <option value="">Seleccione...</option>
                            <option value="Nueva">Nueva</option>
                            <option value="Control">Control</option>
                            <option value="Revisión">Revisión</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_lente">Tipo de Lente</label>
                        <select class="form-control" name="tipo_lente" id="tipo_lente">
                            <option value="">Seleccione...</option>
                            <option value="Simples">Simples</option>
                            <option value="Bifocales">Bifocales</option>
                            <option value="Multifocales">Multifocales</option>
                            <option value="Sol">Sol</option>
                            <option value="Bifocales Sol">Bifocales Sol</option>
                        </select>
                    </div>
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-eye"></i> Graduación - Ojo Derecho (OD)</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="nue_od_esfera">Esfera</label>
                        <input type="text" class="form-control" name="nue_od_esfera" placeholder="Ej: -2.50" style="max-width: 150px;">
                    </div>
                    <div class="col-md-3">
                        <label for="nue_od_cilindro">Cilindro</label>
                        <input type="text" class="form-control" name="nue_od_cilindro" placeholder="Ej: -0.75" style="max-width: 150px;">
                    </div>
                    <div class="col-md-3">
                        <label for="nue_od_eje">Eje</label>
                        <input type="text" class="form-control" name="nue_od_eje" placeholder="Ej: 180" style="max-width: 150px;">
                    </div>
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-eye"></i> Graduación - Ojo Izquierdo (OI)</h5>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="nue_oi_esfera">Esfera</label>
                        <input type="text" class="form-control" name="nue_oi_esfera" placeholder="Ej: -2.50" style="max-width: 120px;">
                    </div>
                    <div class="col-md-2">
                        <label for="nue_oi_cilindro">Cilindro</label>
                        <input type="text" class="form-control" name="nue_oi_cilindro" placeholder="Ej: -0.75" style="max-width: 120px;">
                    </div>
                    <div class="col-md-2">
                        <label for="nue_oi_eje">Eje</label>
                        <input type="text" class="form-control" name="nue_oi_eje" placeholder="Ej: 180" style="max-width: 120px;">
                    </div>
                    <div class="col-md-2">
                        <label for="nue_adicion">Adición</label>
                        <input type="text" class="form-control" name="nue_adicion" placeholder="Ej: +2.50" style="max-width: 120px;">
                    </div>
                </div>
                
                <hr class="my-4">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Notas adicionales"></textarea>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-modern btn-modern-primary">
                            <i class="fas fa-save"></i> Guardar Historia Clínica
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

<script>
$(document).ready(function() {
    var resultadosDiv = $('#resultados-cliente');
    var inputHidden = $('#id_cliente');
    var inputSearch = $('#cliente_search');
    
    // Ocultar resultados al hacer clic fuera
    $(document).click(function() {
        resultadosDiv.hide();
    });
    
    // Buscar clientes
    inputSearch.on('input', function() {
        var query = $(this).val();
        
        if (query.length < 2) {
            resultadosDiv.hide();
            inputHidden.val('');
            return;
        }
        
        $.ajax({
            url: 'ajax.php',
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(data) {
                mostrarResultados(data);
            }
        });
    });
    
    function mostrarResultados(clientes) {
        resultadosDiv.empty();
        
        if (clientes.length === 0) {
            resultadosDiv.html('<div class="p-3 text-muted">No se encontraron clientes</div>');
            resultadosDiv.show();
            return;
        }
        
        clientes.forEach(function(cliente) {
            var item = $('<div class="p-2 border-bottom" style="cursor: pointer; hover: background-color: #f0f0f0;" onmouseover="this.style.backgroundColor=\'#f0f0f0\'" onmouseout="this.style.backgroundColor=\'white\'"></div>');
            item.html('<strong>' + cliente.label + '</strong><br><small>Tel: ' + cliente.telefono + ' - DNI: ' + cliente.dni + '</small>');
            
            item.click(function() {
                inputSearch.val(cliente.label);
                inputHidden.val(cliente.id);
                resultadosDiv.hide();
            });
            
            resultadosDiv.append(item);
        });
        
        resultadosDiv.show();
    }
});
</script>

<?php include_once "includes/footer.php"; ?>
