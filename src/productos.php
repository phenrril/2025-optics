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
            $costo = isset($_POST['costo']) ? 1 : 0;
            $query_insert = mysqli_query($conexion, "INSERT INTO producto(codigo, descripcion, marca, precio, existencia, usuario_id, precio_bruto, costo) VALUES ('$codigo', '$producto', '$marca', '$precio', '$cantidad', '$usuario_id', '$precio_bruto', '$costo')");
            
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

<style>
/* Estilos modernos para productos */
.productos-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 20px;
}

.page-header-modern {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
}

.page-header-modern h2 {
    margin: 0;
    font-weight: 600;
    font-size: 2rem;
}

.btn-modern-icon {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    font-size: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-modern-icon:hover {
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

.card-modern {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
    overflow: hidden;
}

.card-header-modern {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
}

.table-modern {
    border-collapse: separate;
    border-spacing: 0;
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 15px;
    border: none;
}

.table-modern tbody tr {
    transition: all 0.3s;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.badge-custom {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 8px;
    border: none;
    transition: all 0.3s;
    margin-right: 5px;
}

.btn-action:hover {
    transform: scale(1.1);
}
</style>

<div class="productos-container">
    <div class="page-header-modern">
        <h2><i class="fas fa-boxes mr-2"></i> Gestión de Productos</h2>
        <p class="mb-0 mt-2">Administra tus productos de manera eficiente</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-modern-primary btn-modern-icon" type="button" data-toggle="modal" data-target="#nuevo_producto">
            <i class="fas fa-plus mr-2"></i> Nuevo Producto
        </button>
        <div class="d-flex align-items-center">
            <div class="alert alert-light border-0 shadow-sm mb-0 py-2 px-3 mr-3">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                <strong>Total productos con stock:</strong> 
                <span class="badge badge-info" id="total-productos">0</span>
            </div>
            <div class="form-check mb-0" title="Ocultar/mostrar columna Precio Bruto">
                <input class="form-check-input" type="checkbox" id="toggle-precio-bruto" checked>
            </div>
        </div>
    </div>

    <?php echo isset($alert) ? $alert : ''; ?>

<div class="card-modern">
    <div class="card-body-modern">
        <div class="table-responsive">
            <table class="table table-modern" id="tbl">
                <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th class="col-precio-bruto">Precio Bruto</th>
                <th>Costo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Mostrar solo productos con stock (existencia > 0) Y activos (estado = 1)
                $query = mysqli_query($conexion, "SELECT * FROM producto WHERE existencia > 0 AND estado = 1 ORDER BY codproducto DESC");
                $result = mysqli_num_rows($query);
                
                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                        if ($data['estado'] == 1) {
                            $estado = '<span class="badge badge-custom badge-success"><i class="fas fa-check-circle mr-1"></i>Activo</span>';
                        } else {
                            $estado = '<span class="badge badge-custom badge-danger"><i class="fas fa-times-circle mr-1"></i>Inactivo</span>';
                        }
                        
                        // Stock bajo
                        if ($data['existencia'] <= 10 && $data['existencia'] > 0) {
                            $stock_class = 'text-warning';
                            $stock_icon = '<i class="fas fa-exclamation-triangle mr-1"></i>';
                        } elseif ($data['existencia'] <= 0) {
                            $stock_class = 'text-danger';
                            $stock_icon = '<i class="fas fa-times-circle mr-1"></i>';
                        } else {
                            $stock_class = 'text-success';
                            $stock_icon = '<i class="fas fa-check-circle mr-1"></i>';
                        }
                        
                        // Indicador de costo
                        if (isset($data['costo']) && $data['costo'] == 1) {
                            $costo_badge = '<span class="badge badge-custom badge-info"><i class="fas fa-check mr-1"></i>Costo</span>';
                        } else {
                            $costo_badge = '<span class="badge badge-custom badge-secondary"><i class="fas fa-times mr-1"></i>No</span>';
                        }
                ?>
                <tr>
                    <td><?php echo $data['codproducto']; ?></td>
                    <td><?php echo $data['codigo']; ?></td>
                    <td><?php echo $data['descripcion']; ?></td>
                    <td><?php echo $data['marca']; ?></td>
                    <td><?php echo number_format($data['precio'], 2); ?></td>
                    <td><span class="<?php echo $stock_class; ?>"><?php echo $stock_icon; ?><?php echo $data['existencia']; ?></span></td>
                    <td><?php echo $estado; ?></td>
                    <td class="col-precio-bruto"><?php echo number_format($data['precio_bruto'], 2); ?></td>
                    <td><?php echo $costo_badge; ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <?php if ($data['estado'] == 1) { ?>
                                <a href="agregar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-primary btn-sm btn-action" title="Agregar Stock">
                                    <i class='fas fa-plus-circle'></i>
                                </a>
                                <a href="editar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-success btn-sm btn-action" title="Editar">
                                    <i class='fas fa-edit'></i>
                                </a>
                                <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                    <button class="btn btn-danger btn-sm btn-action" type="submit" title="Eliminar">
                                        <i class='fas fa-trash-alt'></i>
                                    </button>
                                </form>
                            <?php } ?>
                            
                            <?php if ($data['estado'] == 0) { ?>
                                <a href="activar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-warning btn-sm btn-action" title="Activar">
                                    <i class='fas fa-check-circle'></i>
                                </a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php }
            } else { ?>
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No hay productos registrados</p>
                    </td>
                </tr>
            <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="costo" id="costo" value="1">
                                    <label class="form-check-label" for="costo">
                                        <i class="fas fa-tag mr-2"></i> Marcar como producto de costo
                                    </label>
                                </div>
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
<div class="card-modern mt-4">
    <div class="card-header-modern">
        <i class="fas fa-tags mr-2"></i> Actualizar Precio por Marca
    </div>
    <div class="card-body-modern">
        <form method="post" id="form_marca">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="fas fa-tag mr-2"></i>Marca</label>
                        <input id="id_marca" class="form-control" type="text" name="id_marca" placeholder="Ingrese la marca" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="fas fa-percent mr-2"></i>Porcentaje</label>
                        <input id="id_porcentaje" class="form-control" type="number" name="id_porcentaje" placeholder="Ingrese el porcentaje" step="0.01" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="button" class="btn btn-modern-primary btn-block btn-modern-icon" id="btn_marca">
                            <i class="fas fa-sync-alt mr-2"></i> Actualizar Precios
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div id="prueba" class="mt-3"></div>
    </div>
</div>
</div> <!-- End productos-container -->

<?php include_once "includes/footer.php"; ?>

<script>
    $(document).ready(function() {
        // Actualizar contador de productos
        var totalProductos = <?php echo $result; ?>;
        $('#total-productos').text(totalProductos);
        
        // Toggle columna Precio Bruto (checked = ocultar)
        function aplicarVisibilidadPrecioBruto() {
            var ocultar = $('#toggle-precio-bruto').is(':checked');
            var $columnas = $('#tbl th.col-precio-bruto, #tbl td.col-precio-bruto');
            if (ocultar) {
                $columnas.hide();
            } else {
                $columnas.show();
            }
        }

        // Estado inicial (checkbox viene checked por defecto → ocultar)
        aplicarVisibilidadPrecioBruto();
        $('#toggle-precio-bruto').on('change', aplicarVisibilidadPrecioBruto);

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
