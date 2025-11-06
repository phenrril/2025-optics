<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();
include "../conexion.php";

// Verificar si hay errores de conexión
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Validar sesión
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}

$id_user = intval($_SESSION['idUser']);
$permiso = "productos";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}

// Validar ID del producto
if (empty($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$id_producto = intval($_GET['id']);
if (!is_numeric($id_producto) || $id_producto <= 0) {
    header("Location: productos.php");
    exit();
}

// Obtener datos del producto
$consulta = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
if (!$consulta) {
    die("Error al consultar producto: " . mysqli_error($conexion));
}

$data_producto = mysqli_fetch_assoc($consulta);
if (!$data_producto) {
    header("Location: productos.php");
    exit();
}

// Procesar formulario
$alert = "";
if (!empty($_POST)) {
    error_log("DEBUG agregar_producto: Formulario recibido - POST data: " . print_r($_POST, true));
    
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : $data_producto['precio'];
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    
    if ($cantidad > 0) {
        $total = $cantidad + $data_producto['existencia'];
        error_log("DEBUG agregar_producto: Actualizando stock - cantidad actual: {$data_producto['existencia']}, agregar: $cantidad, total: $total");
        
        // Actualizar existencia y precio si se proporcionó
        if ($precio > 0 && $precio != $data_producto['precio']) {
            $query_update = mysqli_query($conexion, "UPDATE producto SET existencia = $total, precio = $precio WHERE codproducto = $id_producto");
        } else {
            $query_update = mysqli_query($conexion, "UPDATE producto SET existencia = $total WHERE codproducto = $id_producto");
        }
        
        if ($query_update) {
            error_log("DEBUG agregar_producto: Stock actualizado exitosamente");
            $alert = '<div class="alert alert-success" role="alert">
                        Stock actualizado exitosamente
                    </div>';
            // Recargar datos del producto
            $consulta = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
            $data_producto = mysqli_fetch_assoc($consulta);
        } else {
            error_log("DEBUG agregar_producto: Error al actualizar: " . mysqli_error($conexion));
            $alert = '<div class="alert alert-danger" role="alert">
                        Error al ingresar la cantidad: ' . mysqli_error($conexion) . '
                    </div>';
        }
    } else {
        $alert = '<div class="alert alert-danger" role="alert">
                    La cantidad debe ser mayor a cero
                </div>';
    }
}

include_once "includes/header.php";
?>
<div class="row">
    <div class="col-lg-6 m-auto">
        <div class="card">
            <div class="card-header bg-primary">
                Agregar Producto
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="precio">Precio Actual</label>
                        <input type="text" class="form-control" value="<?php echo $data_producto['precio']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="precio">Cantidad de productos Disponibles</label>
                        <input type="number" class="form-control" value="<?php echo $data_producto['existencia']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="precio">Nuevo Precio</label>
                        <input type="text" placeholder="Ingrese nombre del precio" name="precio" class="form-control" value="<?php echo $data_producto['precio']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Agregar Cantidad</label>
                        <input type="number" placeholder="Ingrese cantidad" name="cantidad" id="cantidad" class="form-control">
                    </div>

                    <input type="submit" value="Actualizar" class="btn btn-primary">
                    <a href="productos.php" class="btn btn-danger">Regresar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>