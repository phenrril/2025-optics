<?php 
session_start();
require_once "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "configuracion";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1){   
    header("location:permisos.php");
    exit();
}
include_once "includes/header.php";
$query = mysqli_query($conexion, "SELECT * FROM configuracion");
$data = mysqli_fetch_assoc($query);
if ($_POST) {
    $alert = '';
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-danger" role="alert">
            Todo los campos son obligatorios
        </div>';
    }else{
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $id = $_POST['id'];
        $update = mysqli_query($conexion, "UPDATE configuracion SET nombre = '$nombre', telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = $id");
        if ($update) {
            $alert = '<div class="alert alert-success" role="alert">
            Datos modificado
        </div>';
        }
    }
}
?>

<style>
/* Estilos modernos para configuración */
.config-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    text-align: center;
}

.page-header-modern h2 {
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

.card-header-modern-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
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

.btn-modern-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.btn-modern-warning:hover {
    background: linear-gradient(135deg, #e081eb 0%, #f04a59 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240, 74, 89, 0.4);
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
    .page-header-modern h2 {
        font-size: 1.5rem;
    }
}
</style>

<div class="config-container fade-in-container">
    <!-- Encabezado -->
    <div class="page-header-modern">
        <h2><i class="fas fa-cog mr-2"></i> Configuración del Sistema</h2>
        <p class="mb-0 mt-2"><i class="fas fa-info-circle mr-1"></i> Gestión de datos de la empresa y configuración</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Formulario Datos Empresa -->
            <div class="card card-modern">
                <div class="card-header-modern">
                    <i class="fas fa-building mr-2"></i> Datos de la Empresa
                </div>
                <div class="card-body card-body-modern">
                    <form action="" method="post">
                        <div class="form-group">
                            <label><i class="fas fa-building mr-2 text-primary"></i> Nombre *</label>
                            <input type="hidden" name="id" value="<?php echo $data['id'] ?>">
                            <input type="text" name="nombre" class="form-control form-control-modern" value="<?php echo htmlspecialchars($data['nombre']); ?>" placeholder="Nombre de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone mr-2 text-success"></i> Teléfono *</label>
                            <input type="text" name="telefono" class="form-control form-control-modern" value="<?php echo htmlspecialchars($data['telefono']); ?>" placeholder="Teléfono de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope mr-2 text-info"></i> Correo Electrónico *</label>
                            <input type="email" name="email" class="form-control form-control-modern" value="<?php echo htmlspecialchars($data['email']); ?>" placeholder="Correo de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt mr-2 text-warning"></i> Dirección *</label>
                            <input type="text" name="direccion" class="form-control form-control-modern" value="<?php echo htmlspecialchars($data['direccion']); ?>" placeholder="Dirección de la Empresa" required>
                        </div>
                        <?php echo isset($alert) ? $alert : ''; ?>
                        <div class="text-center">
                            <button type="submit" class="btn btn-modern btn-modern-primary">
                                <i class="fas fa-save mr-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Gestión de Productos -->
            <div class="card card-modern">
                <div class="card-header-modern card-header-modern-warning">
                    <i class="fas fa-box-open mr-2"></i> Gestión de Productos
                </div>
                <div class="card-body card-body-modern">
                    <p class="mb-4"><i class="fas fa-info-circle text-info mr-2"></i>Ocultar automáticamente todos los productos sin stock en la base de datos.</p>
                    <div id="resultado-ocultar" class="mb-3"></div>
                    <button type="button" class="btn btn-modern btn-modern-warning" id="btnOcultarProductos">
                        <i class="fas fa-eye-slash mr-2"></i> Ocultar Productos Sin Stock
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnOcultarProductos').click(function() {
        // Deshabilitar el botón mientras se procesa
        $(this).prop('disabled', true);
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        // Limpiar resultado anterior
        $('#resultado-ocultar').html('');
        
        // Hacer la petición AJAX
        $.ajax({
            url: 'ocultar_productos_sin_stock.php',
            type: 'POST',
            data: {},
            dataType: 'json',
            success: function(response) {
                $('#resultado-ocultar').html(response.html);
                $('#btnOcultarProductos').prop('disabled', false);
                $('#btnOcultarProductos').html('<i class="fas fa-eye-slash"></i> Ocultar Productos Sin Stock');
                
                // Si fue exitoso, mostrar SweetAlert
                if (response.success) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Productos Ocultados',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            },
            error: function() {
                $('#resultado-ocultar').html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                $('#btnOcultarProductos').prop('disabled', false);
                $('#btnOcultarProductos').html('<i class="fas fa-eye-slash"></i> Ocultar Productos Sin Stock');
            }
        });
    });
});
</script>

<?php include_once "includes/footer.php"; ?>