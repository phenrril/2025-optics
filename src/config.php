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

<div class="row">
<div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Datos de la Empresa
                </div>
                <div class="card-body">
                    <form action="" method="post" class="p-3">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="hidden" name="id" value="<?php echo $data['id'] ?>">
                            <input type="text" name="nombre" class="form-control" value="<?php echo $data['nombre']; ?>" id="txtNombre" placeholder="Nombre de la Empresa" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="number" name="telefono" class="form-control" value="<?php echo $data['telefono']; ?>" id="txtTelEmpresa" placeholder="teléfono de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico:</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" id="txtEmailEmpresa" placeholder="Correo de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección:</label>
                            <input type="text" name="direccion" class="form-control" value="<?php echo $data['direccion']; ?>" id="txtDirEmpresa" placeholder="Dirreción de la Empresa" required>
                        </div>
                        <?php echo isset($alert) ? $alert : ''; ?>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modificar Datos</button>
                        </div>

                    </form>
                </div>
            </div>
            
            <!-- Card para ocultar productos sin stock -->
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-box-open"></i> Gestión de Productos</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Ocultar automáticamente todos los productos sin stock en la base de datos.</p>
                    <div id="resultado-ocultar" class="mb-3"></div>
                    <button type="button" class="btn btn-warning" id="btnOcultarProductos">
                        <i class="fas fa-eye-slash"></i> Ocultar Productos Sin Stock
                    </button>
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