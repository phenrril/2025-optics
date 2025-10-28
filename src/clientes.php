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
include_once "includes/header.php";
if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-danger" role="alert">
                                    Complete los campos obligatorios
                                </div>';
    } else {
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $usuario_id = $_SESSION['idUser'];
        $dni = $_POST['dni'];
        $obrasocial = $_POST['obrasocial'];
        $medico = $_POST['medico'];
        $result = 0;
        $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre = '$nombre'");
        $result = mysqli_fetch_array($query);
        if ($result > 0) {
            $alert = '<div class="alert alert-danger" role="alert">
                                    El cliente ya existe
                                </div>';
        } else {
            $query_insert = mysqli_query($conexion, "INSERT INTO cliente(nombre,telefono,direccion, usuario_id, dni, obrasocial, medico) values ('$nombre', '$telefono', '$direccion', '$usuario_id', '$dni', '$obrasocial', '$medico')");
            if ($query_insert) {
                $alert = '<div class="alert alert-success" role="alert">
                                    Cliente registrado
                                </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                                    Error al registrar
                            </div>';
            }
        }
    }
    mysqli_close($conexion);
}
?>

<style>
/* Estilos modernos para clientes */
.clientes-container {
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

.card-body-modern {
    padding: 0;
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
</style>

<div class="clientes-container fade-in-container">
    <div class="page-header-modern">
        <h2><i class="fas fa-users mr-2"></i> Gestión de Clientes</h2>
        <p class="mb-0 mt-2">Administra tus clientes de manera eficiente</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-modern-primary btn-modern-icon" type="button" data-toggle="modal" data-target="#nuevo_cliente">
            <i class="fas fa-plus mr-2"></i> Nuevo Cliente
        </button>
        <?php
        include "../conexion.php";
        $query_count = mysqli_query($conexion, "SELECT COUNT(*) as total FROM cliente WHERE estado = 1");
        $total_clientes = mysqli_fetch_assoc($query_count);
        ?>
        <div class="alert alert-light border-0 shadow-sm mb-0 py-2 px-3">
            <i class="fas fa-info-circle text-primary mr-2"></i>
            <strong>Total clientes activos:</strong> 
            <span class="badge badge-info"><?php echo $total_clientes['total']; ?></span>
        </div>
    </div>

    <?php echo isset($alert) ? $alert : ''; ?>

    <div class="card-modern">
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table table-modern" id="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre *</th>
                            <th>Teléfono *</th>
                            <th>Dirección *</th>      
                            <th>DNI</th>
                            <th>Obra Social</th>
                            <th>Médico</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "../conexion.php";

                        $query = mysqli_query($conexion, "SELECT * FROM cliente ORDER BY idcliente DESC");
                        $result = mysqli_num_rows($query);
                        if ($result > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                                if ($data['estado'] == 1) {
                                    $estado = '<span class="badge badge-custom badge-success"><i class="fas fa-check-circle mr-1"></i>Activo</span>';
                                } else {
                                    $estado = '<span class="badge badge-custom badge-danger"><i class="fas fa-times-circle mr-1"></i>Inactivo</span>';
                                }
                        ?>
                                <tr>
                                    <td><?php echo $data['idcliente']; ?></td>
                                    <td><i class="fas fa-user-circle text-primary mr-2"></i><?php echo htmlspecialchars($data['nombre']); ?></td>
                                    <td><i class="fas fa-phone text-success mr-2"></i><?php echo htmlspecialchars($data['telefono']); ?></td>
                                    <td><i class="fas fa-map-marker-alt text-info mr-2"></i><?php echo htmlspecialchars($data['direccion']); ?></td>             
                                    <td><?php echo htmlspecialchars($data['dni'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($data['obrasocial'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($data['medico'] ?: '-'); ?></td>
                                    <td><?php echo $estado; ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if ($data['estado'] == 1) { ?>
                                                <a href="editar_cliente.php?id=<?php echo $data['idcliente']; ?>" class="btn btn-success btn-sm btn-action" title="Editar">
                                                    <i class='fas fa-edit'></i>
                                                </a>
                                                <form action="eliminar_cliente.php?id=<?php echo $data['idcliente']; ?>" method="post" class="confirmar d-inline">
                                                    <button class="btn btn-danger btn-sm btn-action" type="submit" title="Eliminar">
                                                        <i class='fas fa-trash-alt'></i>
                                                    </button>
                                                </form>
                                            <?php } ?>

                                            <?php if ($data['estado'] == 0) { ?>
                                                <form action="reactivar_cliente.php?id=<?php echo $data['idcliente']; ?>" method="post" class="d-inline">
                                                    <button class="btn btn-warning btn-sm btn-action" type="submit" title="Reactivar">
                                                        <i class='fas fa-redo'></i>
                                                    </button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No hay clientes registrados</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div id="nuevo_cliente" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">
                    <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                </h5>
                <button class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre"><i class="fas fa-user mr-2 text-primary"></i>Nombre *</label>
                                <input type="text" placeholder="Ingrese nombre del cliente" name="nombre" id="nombre" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono"><i class="fas fa-phone mr-2 text-success"></i>Teléfono *</label>
                                <input type="text" placeholder="Ingrese teléfono" name="telefono" id="telefono" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion"><i class="fas fa-map-marker-alt mr-2 text-info"></i>Dirección *</label>
                                <input type="text" placeholder="Ingrese dirección" name="direccion" id="direccion" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dni"><i class="fas fa-id-card mr-2 text-warning"></i>DNI</label>
                                <input type="text" placeholder="Ingrese DNI" name="dni" id="dni" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="obrasocial"><i class="fas fa-hospital mr-2 text-danger"></i>Obra Social</label>
                                <input type="text" placeholder="Ingrese obra social" name="obrasocial" id="obrasocial" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="medico"><i class="fas fa-user-md mr-2 text-secondary"></i>Médico</label>
                                <input type="text" placeholder="Ingrese médico" name="medico" id="medico" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>