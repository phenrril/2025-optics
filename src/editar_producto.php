<?php
session_start();
include "../conexion.php";

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

include_once "includes/header.php";
if (!empty($_POST)) {
  $alert = "";
  if (empty($_POST['codigo']) || empty($_POST['producto']) || empty($_POST['precio'])) {
    $alert = '<div class="alert alert-primary" role="alert">
              Complete los campos requeridos
            </div>';
  } else {
    $codproducto = intval($_GET['id']);
    $codigo = mysqli_real_escape_string($conexion, $_POST['codigo']);
    $producto = mysqli_real_escape_string($conexion, $_POST['producto']);
    $precio = floatval($_POST['precio']);
    $cantidad = intval($_POST['cantidad']);
    $usuario_id = $_SESSION['idUser'];
    $precio_bruto = floatval($_POST['precio_bruto']);
    $marca = mysqli_real_escape_string($conexion, $_POST['marca']);
    $costo = isset($_POST['costo']) ? 1 : 0;
    
    // Verificar si existe la columna costo
    $check_column = mysqli_query($conexion, "SHOW COLUMNS FROM producto LIKE 'costo'");
    $column_exists = mysqli_num_rows($check_column) > 0;
    
    if ($column_exists) {
        // Si existe la columna, incluirla en el UPDATE
        $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio = '$precio', marca = '$marca', precio_bruto = '$precio_bruto', costo = '$costo' WHERE codproducto = $codproducto");
    } else {
        // Si no existe, no incluir el campo costo
        $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio = '$precio', marca = '$marca', precio_bruto = '$precio_bruto' WHERE codproducto = $codproducto");
    }
    if ($query_update) {
      $alert = '<div class="alert alert-primary" role="alert">
              Producto Modificado
            </div>';
    } else {
      $alert = '<div class="alert alert-primary" role="alert">
                Error al Modificar
              </div>';
    }
  }
}

// Validar producto

if (empty($_REQUEST['id'])) {
  header("Location: productos.php");
} else {
  $id_producto = intval($_REQUEST['id']);
  if ($id_producto <= 0) {
    header("Location: productos.php");
    exit();
  }
  $query_producto = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
  $result_producto = mysqli_num_rows($query_producto);

  if ($result_producto > 0) {
    $data_producto = mysqli_fetch_assoc($query_producto);
  } else {
    header("Location: productos.php");
    exit();
  }
}
?>
<div class="row">
  <div class="col-lg-6 m-auto">

    <div class="card">
      <div class="card-header bg-primary text-white">
        Modificar producto
      </div>
      <div class="card-body">
        <form action="" method="post">
          <?php echo isset($alert) ? $alert : ''; ?>
          <div class="form-group">
            <label for="codigo">Código de Barras</label>
            <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control" value="<?php echo $data_producto['codigo']; ?>">
          </div>
          <div class="form-group">
            <label for="producto">Producto</label>
            <input type="text" class="form-control" placeholder="Ingrese nombre del producto" name="producto" id="producto" value="<?php echo $data_producto['descripcion']; ?>">
          </div>
          <div class="form-group">
            <label for="marca">Marca</label>
            <input type="text" placeholder="Ingrese la marca" class="form-control" name="marca" id="marca" value="<?php echo isset($data_producto['marca']) ? $data_producto['marca'] : ''; ?>">
          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio" value="<?php echo $data_producto['precio']; ?>">
          </div>
          <div class="form-group">
            <label for="precio_bruto">Precio Bruto</label>
            <input type="text" placeholder="Ingrese precio Bruto" class="form-control" name="precio_bruto" id="precio_bruto" value="<?php echo isset($data_producto['precio_bruto']) ? $data_producto['precio_bruto'] : ''; ?>">
          </div>
          <div class="form-group">
            <label for="cantidad">Stock</label>
            <input type="number" placeholder="Ingrese Stock" class="form-control" name="cantidad" id="cantidad" value="<?php echo isset($data_producto['existencia']) ? $data_producto['existencia'] : ''; ?>">
          </div>
          <?php 
          // Verificar si existe la columna costo
          $check_column_display = mysqli_query($conexion, "SHOW COLUMNS FROM producto LIKE 'costo'");
          $column_exists_display = mysqli_num_rows($check_column_display) > 0;
          if ($column_exists_display) { ?>
          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="costo" id="costo" value="1" <?php echo (isset($data_producto['costo']) && $data_producto['costo'] == 1) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="costo">
                <i class="fas fa-tag mr-2"></i> Marcar como producto de costo
              </label>
            </div>
          </div>
          <?php } ?>
          <input type="submit" value="Actualizar Producto" class="btn btn-primary">
          <a href="productos.php" class="btn btn-danger">Atras</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once "includes/footer.php"; ?>