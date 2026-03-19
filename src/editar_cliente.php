<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}

$id_user = $_SESSION['idUser'];
$permiso = "clientes";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}

if (empty($_REQUEST['id'])) {
    header("Location: clientes.php");
    exit();
}

$idcliente = (int) $_REQUEST['id'];
$sql = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
if (mysqli_num_rows($sql) == 0) {
    header("Location: clientes.php");
    exit();
}
$data = mysqli_fetch_assoc($sql);
$idcliente  = $data['idcliente'];
$nombre     = $data['nombre'];
$telefono   = $data['telefono'];
$direccion  = $data['direccion'];
$dni        = $data['dni'];
$obrasocial = $data['obrasocial'];
$medico     = $data['medico'];

$alert = '';
if (!empty($_POST)) {
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-danger" role="alert">Complete los campos requeridos</div>';
    } else {
        $idcliente   = (int) $_POST['id'];
        $nombre      = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $telefono    = mysqli_real_escape_string($conexion, $_POST['telefono']);
        $direccion   = mysqli_real_escape_string($conexion, $_POST['direccion']);
        $dni         = mysqli_real_escape_string($conexion, $_POST['dni']);
        $obrasocial  = mysqli_real_escape_string($conexion, $_POST['obrasocial']);
        $medico      = mysqli_real_escape_string($conexion, $_POST['medico']);
        $sql_update  = mysqli_query($conexion, "UPDATE cliente SET nombre='$nombre', telefono='$telefono', direccion='$direccion', dni='$dni', obrasocial='$obrasocial', medico='$medico' WHERE idcliente=$idcliente");
        if ($sql_update) {
            $alert = '<div class="alert alert-success" role="alert">Cliente actualizado correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el cliente</div>';
        }
    }
}

include_once "includes/header.php";
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Modificar Cliente
                </div>
                <div class="card-body">
                    <form class="" action="" method="post">
                        <?php echo isset($alert) ? $alert : ''; ?>
                        <input type="hidden" name="id" value="<?php echo $idcliente; ?>">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" placeholder="Ingrese Nombre" name="nombre" class="form-control" id="nombre" value="<?php echo $nombre; ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="number" placeholder="Ingrese Teléfono" name="telefono" class="form-control" id="telefono" value="<?php echo $telefono; ?>">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" placeholder="Ingrese Direccion" name="direccion" class="form-control" id="direccion" value="<?php echo $direccion; ?>">
                        </div>
                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" placeholder="Ingrese Documento" name="dni" id="dni" class="form-control" value="<?php echo htmlspecialchars($dni); ?>">
                        </div>
                        <div class="form-group">
                            <label for="obrasocial">Obra Social</label>
                            <input type="text" placeholder="Ingrese Obra Social" name="obrasocial" id="obrasocial" class="form-control" value="<?php echo htmlspecialchars($obrasocial); ?>">
                        </div>
                        <div class="form-group">
                            <label for="medico">Médico</label>
                            <input type="text" placeholder="Ingrese Medico" name="medico" id="medico" class="form-control" value="<?php echo htmlspecialchars($medico); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Editar Cliente</button>
                        <a href="clientes.php" class="btn btn-danger">Atras</a>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>
<!-- /.container-fluid -->
<?php include_once "includes/footer.php"; ?>