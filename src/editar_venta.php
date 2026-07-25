<?php
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['idUser'];
$permiso = "ventas";
$permiso_escaped = mysqli_real_escape_string($conexion, $permiso);
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_escaped'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
    exit();
}

$id_venta = isset($_GET['v']) ? intval($_GET['v']) : 0;

$venta = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT v.*, c.nombre AS cliente_nombre FROM ventas v LEFT JOIN cliente c ON v.id_cliente = c.idcliente WHERE v.id = $id_venta"));

if (!$venta) {
    header("Location: lista_ventas.php");
    exit();
}

// Venta ya facturada ante AFIP: no editable ni anulable
$facturada = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT 1 FROM facturas_electronicas WHERE id_venta = $id_venta AND estado = 'aprobado' LIMIT 1"));
if ($facturada) {
    header("Location: lista_ventas.php");
    exit();
}

$detalle = mysqli_query($conexion, "SELECT d.*, p.codigo, p.descripcion FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id_venta ORDER BY d.id ASC");
$graduacion = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM graduaciones WHERE id_venta = $id_venta LIMIT 1"));
if (!$graduacion) {
    $graduacion = array(
        'od_l_1' => '', 'od_l_2' => '', 'od_l_3' => '',
        'oi_l_1' => '', 'oi_l_2' => '', 'oi_l_3' => '',
        'od_c_1' => '', 'od_c_2' => '', 'od_c_3' => '',
        'oi_c_1' => '', 'oi_c_2' => '', 'oi_c_3' => '',
        'addg' => '', 'obs' => ''
    );
}

include_once "includes/header.php";
?>

<link rel="stylesheet" href="../assets/js/jquery-ui/jquery-ui.min.css">

<style>
.editar-venta-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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
    padding: 15px 25px;
    font-weight: 600;
}

.card-body-modern {
    padding: 25px;
}

.precio-editable, .cantidad-editable {
    width: 100px;
    border: 1.5px dashed #b0bec5;
    border-radius: 6px;
    padding: 4px 8px;
}

#total_venta_badge {
    font-size: 1.3rem;
}
</style>

<div class="editar-venta-container">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2><i class="fas fa-edit mr-2"></i> Editar Venta #<?php echo $id_venta; ?></h2>
            <p class="mb-0 mt-2"><i class="fas fa-user mr-1"></i> <?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'Sin cliente'); ?></p>
        </div>
        <div class="text-right">
            <span class="badge badge-light" id="total_venta_badge">Total: $<span id="total_venta"><?php echo number_format($venta['total'], 2); ?></span></span>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header-modern"><i class="fas fa-shopping-cart mr-2"></i> Productos</div>
        <div class="card-body card-body-modern">
            <table class="table" id="tbl_detalle">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio unit.</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($detalle)) { ?>
                        <tr data-id="<?php echo $row['id']; ?>">
                            <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                            <td>
                                <input type="number" min="1" class="form-control cantidad-editable" value="<?php echo intval($row['cantidad']); ?>" data-id="<?php echo $row['id']; ?>">
                            </td>
                            <td>
                                <input type="number" min="0" step="0.01" class="form-control precio-editable" value="<?php echo floatval($row['precio']); ?>" data-id="<?php echo $row['id']; ?>">
                            </td>
                            <td class="subtotal-linea">$<?php echo number_format($row['precio'] * $row['cantidad'], 2); ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-eliminar-linea" data-id="<?php echo $row['id']; ?>" title="Eliminar producto de la venta">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <hr>
            <div class="row">
                <div class="col-md-8">
                    <label>Agregar producto</label>
                    <input type="text" id="buscar_producto" class="form-control" placeholder="Buscar por código o descripción (min. 3 letras)">
                </div>
                <div class="col-md-2">
                    <label>Cantidad</label>
                    <input type="number" id="cantidad_agregar" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success btn-block" id="btn_agregar_producto"><i class="fas fa-plus mr-1"></i> Agregar</button>
                </div>
            </div>
            <input type="hidden" id="id_producto_seleccionado">
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><i class="fas fa-glasses mr-2"></i> Graduación</div>
        <div class="card-body card-body-modern">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th></th>
                            <th colspan="3">Lejos</th>
                            <th colspan="3">Cerca</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Esférico</th><th>Cilíndrico</th><th>Eje</th>
                            <th>Esférico</th><th>Cilíndrico</th><th>Eje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>OD</strong></td>
                            <td><input class="form-control grad-input" id="od_l_1" value="<?php echo htmlspecialchars($graduacion['od_l_1']); ?>"></td>
                            <td><input class="form-control grad-input" id="od_l_2" value="<?php echo htmlspecialchars($graduacion['od_l_2']); ?>"></td>
                            <td><input class="form-control grad-input" id="od_l_3" value="<?php echo htmlspecialchars($graduacion['od_l_3']); ?>"></td>
                            <td><input class="form-control grad-input" id="od_c_1" value="<?php echo htmlspecialchars($graduacion['od_c_1']); ?>"></td>
                            <td><input class="form-control grad-input" id="od_c_2" value="<?php echo htmlspecialchars($graduacion['od_c_2']); ?>"></td>
                            <td><input class="form-control grad-input" id="od_c_3" value="<?php echo htmlspecialchars($graduacion['od_c_3']); ?>"></td>
                        </tr>
                        <tr>
                            <td><strong>OI</strong></td>
                            <td><input class="form-control grad-input" id="oi_l_1" value="<?php echo htmlspecialchars($graduacion['oi_l_1']); ?>"></td>
                            <td><input class="form-control grad-input" id="oi_l_2" value="<?php echo htmlspecialchars($graduacion['oi_l_2']); ?>"></td>
                            <td><input class="form-control grad-input" id="oi_l_3" value="<?php echo htmlspecialchars($graduacion['oi_l_3']); ?>"></td>
                            <td><input class="form-control grad-input" id="oi_c_1" value="<?php echo htmlspecialchars($graduacion['oi_c_1']); ?>"></td>
                            <td><input class="form-control grad-input" id="oi_c_2" value="<?php echo htmlspecialchars($graduacion['oi_c_2']); ?>"></td>
                            <td><input class="form-control grad-input" id="oi_c_3" value="<?php echo htmlspecialchars($graduacion['oi_c_3']); ?>"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label>Adición</label>
                    <input class="form-control grad-input" id="addg" value="<?php echo htmlspecialchars($graduacion['addg']); ?>">
                </div>
                <div class="col-md-9">
                    <label>Observaciones</label>
                    <input class="form-control grad-input" id="obs" value="<?php echo htmlspecialchars($graduacion['obs']); ?>">
                </div>
            </div>
            <div class="text-right mt-3">
                <button class="btn btn-primary" id="btn_guardar_graduacion"><i class="fas fa-save mr-1"></i> Guardar graduación</button>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="lista_ventas.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Volver</a>
        <div>
            <a href="pdf/generar.php?cl=<?php echo $venta['id_cliente']; ?>&v=<?php echo $id_venta; ?>" target="_blank" class="btn btn-info"><i class="fas fa-file-pdf mr-1"></i> Ver PDF</a>
            <button class="btn btn-danger" id="btn_anular_venta"><i class="fas fa-ban mr-1"></i> Anular venta completa</button>
        </div>
    </div>
</div>

<script>
const ID_VENTA = <?php echo $id_venta; ?>;

function actualizarTotal(total) {
    $('#total_venta').text(parseFloat(total).toFixed(2));
}

function recalcularSubtotalFila($fila) {
    const cantidad = parseFloat($fila.find('.cantidad-editable').val()) || 0;
    const precio = parseFloat($fila.find('.precio-editable').val()) || 0;
    $fila.find('.subtotal-linea').text('$' + (cantidad * precio).toFixed(2));
}

$(document).ready(function () {
    $('#buscar_producto').autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: 'ajax.php',
                dataType: 'json',
                data: { pro: request.term },
                success: function (data) { response(data); }
            });
        },
        select: function (event, ui) {
            $('#buscar_producto').val(ui.item.label);
            $('#id_producto_seleccionado').val(ui.item.id);
            return false;
        }
    });

    $('#btn_agregar_producto').click(function () {
        const id_producto = $('#id_producto_seleccionado').val();
        const cantidad = parseInt($('#cantidad_agregar').val()) || 1;

        if (!id_producto) {
            Swal.fire({ icon: 'warning', title: 'Buscá y seleccioná un producto de la lista', showConfirmButton: false, timer: 2000 });
            return;
        }

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { agregar_detalle_venta: 1, id_venta: ID_VENTA, id_producto: id_producto, cantidad: cantidad },
            success: function (resp) {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Producto agregado', showConfirmButton: false, timer: 1500 });
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: resp.mensaje || 'Error al agregar producto' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            }
        });
    });

    $(document).on('change', '.cantidad-editable', function () {
        const $input = $(this);
        const $fila = $input.closest('tr');
        const id_detalle = $input.data('id');
        const cantidad = parseInt($input.val());

        if (!cantidad || cantidad < 1) {
            Swal.fire({ icon: 'error', title: 'Cantidad inválida' });
            return;
        }

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { editar_detalle_cantidad: 1, id_detalle: id_detalle, cantidad: cantidad },
            success: function (resp) {
                if (resp.success) {
                    recalcularSubtotalFila($fila);
                    actualizarTotal(resp.total);
                } else {
                    Swal.fire({ icon: 'error', title: resp.mensaje || 'Error al actualizar cantidad' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            }
        });
    });

    $(document).on('change', '.precio-editable', function () {
        const $input = $(this);
        const $fila = $input.closest('tr');
        const id_detalle = $input.data('id');
        const precio = parseFloat($input.val());

        if (isNaN(precio) || precio < 0) {
            Swal.fire({ icon: 'error', title: 'Precio inválido' });
            return;
        }

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { editar_detalle_precio: 1, id_detalle: id_detalle, precio: precio },
            success: function (resp) {
                if (resp.success) {
                    recalcularSubtotalFila($fila);
                    actualizarTotal(resp.total);
                } else {
                    Swal.fire({ icon: 'error', title: resp.mensaje || 'Error al actualizar precio' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            }
        });
    });

    $(document).on('click', '.btn-eliminar-linea', function () {
        const $btn = $(this);
        const id_detalle = $btn.data('id');

        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar este producto de la venta?',
            text: 'El stock se restaura automáticamente.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: { eliminar_detalle_venta: 1, id_detalle: id_detalle },
                success: function (resp) {
                    if (resp.success) {
                        $btn.closest('tr').remove();
                        actualizarTotal(resp.total);
                    } else {
                        Swal.fire({ icon: 'error', title: resp.mensaje || 'Error al eliminar producto' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error de conexión' });
                }
            });
        });
    });

    $('#btn_guardar_graduacion').click(function () {
        const data = { guardar_graduacion_venta: 1, id_venta: ID_VENTA };
        $('.grad-input').each(function () {
            data[$(this).attr('id')] = $(this).val();
        });

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (resp) {
                if (resp.success) {
                    Swal.fire({ icon: 'success', title: 'Graduación guardada', showConfirmButton: false, timer: 1500 });
                } else {
                    Swal.fire({ icon: 'error', title: resp.mensaje || 'Error al guardar graduación' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            }
        });
    });

    $('#btn_anular_venta').click(function () {
        Swal.fire({
            icon: 'warning',
            title: '¿Anular esta venta por completo?',
            text: 'Se eliminará la venta entera y se restaurará el stock de todos los productos. Esta acción no se puede deshacer.',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, anular todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: 'anular.php',
                type: 'POST',
                data: { idanular: ID_VENTA },
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Venta anulada', showConfirmButton: false, timer: 1500 })
                        .then(() => { window.location.href = 'lista_ventas.php'; });
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error al anular la venta' });
                }
            });
        });
    });
});
</script>

<?php include_once "includes/footer.php"; ?>
