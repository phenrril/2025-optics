<?php 
include_once "includes/header.php";
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

if (!empty($_POST)) {
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $usuario_id = intval($_SESSION['idUser']);
    $precio_bruto = $_POST['precio_bruto'];
    $marca = $_POST['marca'];
    $alert = "";
    
    if (empty($codigo) || empty($producto)|| empty($marca) || empty($precio) || $precio < 0 || empty($cantidad) || $cantidad < 0 || empty($precio_bruto) || $precio_bruto < 0) {
        $alert = '<div class="alert alert-danger" role="alert">
            Todos los campos son obligatorios
          </div>';
    } else {
        // Sanitizar variables
        $codigo = mysqli_real_escape_string($conexion, $codigo);
        $producto = mysqli_real_escape_string($conexion, $producto);
        $marca = mysqli_real_escape_string($conexion, $marca);
        $precio = floatval($precio);
        $cantidad = intval($cantidad);
        $precio_bruto = floatval($precio_bruto);
        
        $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
        $result = mysqli_fetch_array($query);
        
        if ($result > 0) {
            $alert = '<div class="alert alert-warning" role="alert">
                El código ya existe
            </div>';
        } else {
            $query_insert = mysqli_query($conexion, "INSERT INTO producto(codigo, descripcion, marca, precio, existencia, usuario_id, precio_bruto) VALUES ('$codigo', '$producto', '$marca', '$precio', '$cantidad', '$usuario_id', '$precio_bruto')");
            
            if ($query_insert) {
                $alert = '<div class="alert alert-success" role="alert">
                    Producto Registrado
                </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                    Error al registrar el producto
                </div>';
            }
        }
    }
}
?>

<button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_producto"><i class="fas fa-plus"></i></button>
<?php echo isset($alert) ? $alert : ''; ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Precio Bruto</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Mostrar solo productos con stock (existencia > 0)
                $query = mysqli_query($conexion, "SELECT * FROM producto WHERE existencia > 0 ORDER BY codproducto DESC");
                $result = mysqli_num_rows($query);
                
                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                        if ($data['estado'] == 1) {
                            $estado = '<span class="badge badge-pill badge-success">Activo</span>';
                        } else {
                            $estado = '<span class="badge badge-pill badge-danger">Inactivo</span>';
                        }
                ?>
                <tr>
                    <td><?php echo $data['codproducto']; ?></td>
                    <td><?php echo $data['codigo']; ?></td>
                    <td><?php echo $data['descripcion']; ?></td>
                    <td><?php echo $data['marca']; ?></td>
                    <td><?php echo number_format($data['precio'], 2); ?></td>
                    <td><?php echo $data['existencia']; ?></td>
                    <td><?php echo $estado; ?></td>
                    <td><?php echo number_format($data['precio_bruto'], 2); ?></td>
                    <td>
                        <?php if ($data['estado'] == 1) { ?>
                            <a href="agregar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-primary btn-sm" title="Ver detalles">
                                <i class='fas fa-audio-description'></i>
                            </a>
                            <a href="editar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-success btn-sm" title="Editar">
                                <i class='fas fa-edit'></i>
                            </a>
                            <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                <button class="btn btn-danger btn-sm" type="submit" title="Eliminar">
                                    <i class='fas fa-trash-alt'></i>
                                </button>
                            </form>
                        <?php } ?>
                        
                        <?php if ($data['estado'] == 0) { ?>
                            <a href="activar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-warning btn-sm" title="Activar">
                                <i class='fas fa-check-circle'></i>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            <?php }
            } else { ?>
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No hay productos registrados</p>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Nuevo Producto -->
<div id="nuevo_producto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Producto</h5>
                <button class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo">Código de Barras *</label>
                                <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="producto">Nombre del Producto *</label>
                                <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="marca">Marca *</label>
                                <input type="text" placeholder="Ingrese la marca" name="marca" id="marca" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cantidad">Stock *</label>
                                <input type="number" placeholder="Cantidad en stock" class="form-control" name="cantidad" id="cantidad" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio">Precio de Venta *</label>
                                <input type="number" step="0.01" placeholder="0.00" class="form-control" name="precio" id="precio" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_bruto">Precio de Compra *</label>
                                <input type="number" step="0.01" placeholder="0.00" class="form-control" name="precio_bruto" id="precio_bruto" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Actualizar Precio por Marca -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-tags mr-2"></i> Actualizar Precio por Marca</h5>
            </div>
            <div class="card-body">
                <form method="post" id="form_marca">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Marca</label>
                                <input id="id_marca" class="form-control" type="text" name="id_marca" placeholder="Ingrese la marca" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Porcentaje</label>
                                <input id="id_porcentaje" class="form-control" type="number" name="id_porcentaje" placeholder="Ingrese el porcentaje" step="0.01" min="0" max="100" required>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="button" class="btn btn-info btn-block" id="btn_marca">
                                    <i class="fas fa-sync-alt mr-2"></i> Actualizar Precios
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="prueba" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

<script>
    $(document).ready(function() {
        $("#btn_marca").click(function() {
            $.ajax({
                url: "actualizar_porcentaje.php",
                type: "post",
                data: $("#form_marca").serialize(),
                success: function(resultado) {
                    $("#prueba").html(resultado);
                },
                error: function() {
                    $("#prueba").html('<div class="alert alert-danger">Error al actualizar precios</div>');
                }
            });
        });
    });
</script>
