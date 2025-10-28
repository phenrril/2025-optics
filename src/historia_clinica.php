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

// Buscar historias por cliente si se envió búsqueda
$where_clause = "";
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $buscar = mysqli_real_escape_string($conexion, $_GET['buscar']);
    $where_clause = "WHERE c.nombre LIKE '%$buscar%' OR c.dni LIKE '%$buscar%'";
}
?>

<style>
/* Estilos modernos consistentes con ventas.php */
.ventas-container {
    max-width: 1400px;
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

.page-header h2 {
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
}

.card-body-modern {
    padding: 25px;
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

.btn-modern-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-modern-success:hover {
    background: linear-gradient(135deg, #0f8680 0%, #2dd66c 100%);
    color: white;
}

.btn-modern-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-modern-info:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a4190 100%);
    color: white;
}

.btn-modern-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
}

.btn-modern-danger:hover {
    background: linear-gradient(135deg, #d42d3f 0%, #e05039 100%);
    color: white;
}

.btn-modern-secondary {
    background: linear-gradient(135deg, #868e96 0%, #495057 100%);
    color: white;
}

.btn-modern-secondary:hover {
    background: linear-gradient(135deg, #777e86 0%, #40454d 100%);
    color: white;
}

.search-section {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(253, 203, 110, 0.3);
    margin-bottom: 25px;
}

.search-input {
    border: 3px solid white;
    border-radius: 12px;
    padding: 15px 20px;
    font-size: 1rem;
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    outline: none;
}

.table-modern {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table-modern thead th {
    border: none;
    padding: 15px;
    font-weight: 600;
    font-size: 0.95rem;
}

.table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    transition: all 0.3s;
}

.badge-modern {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}

.btn-sm-modern {
    padding: 8px 15px;
    font-size: 0.85rem;
    border-radius: 8px;
}
</style>

<div class="ventas-container">
    <div class="page-header">
        <h2><i class="fas fa-clipboard-list"></i> Historias Clínicas</h2>
        <p class="mb-0" style="opacity: 0.9;">Gestión de historias clínicas de pacientes</p>
    </div>

    <div class="card-modern">
        <div class="card-header-modern">
            <i class="fas fa-list"></i> Listado de Historias Clínicas
        </div>
        <div class="card-body-modern">
            <!-- Búsqueda moderna -->
            <div class="search-section">
                <form method="GET" action="" class="d-flex flex-column flex-md-row gap-3">
                    <input type="text" name="buscar" class="form-control search-input flex-grow-1" placeholder="🔍 Buscar por nombre o DNI..." value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                    <button type="submit" class="btn btn-modern btn-modern-info">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <?php if (!empty($where_clause)) { ?>
                        <a href="historia_clinica.php" class="btn btn-modern btn-modern-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    <?php } ?>
                </form>
            </div>
            
            <!-- Botón agregar -->
            <div class="mb-4">
                <a href="agregar_historia.php" class="btn btn-modern btn-modern-primary">
                    <i class="fas fa-plus"></i> Nueva Historia Clínica
                </a>
            </div>

            <!-- Tabla de historias -->
            <div class="table-responsive">
                <table class="table table-modern" id="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Tipo Consulta</th>
                            <th>Tipo Lente</th>
                            <th>Graduación OD</th>
                            <th>Graduación OI</th>
                            <th>Adición</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT h.*, c.nombre as nombre_cliente, c.dni 
                                  FROM historia_clinica h 
                                  INNER JOIN cliente c ON h.id_cliente = c.idcliente 
                                  $where_clause
                                  ORDER BY h.fecha DESC 
                                  LIMIT 100";
                        $result = mysqli_query($conexion, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $fecha = date("d/m/Y", strtotime($row['fecha']));
                                
                                // Formatear graduación OD
                                $grad_od = trim($row['nue_od_esfera'] . ' / ' . $row['nue_od_cilindro'] . ' x ' . $row['nue_od_eje']);
                                if ($grad_od == ' /  x ') $grad_od = '-';
                                
                                // Formatear graduación OI
                                $grad_oi = trim($row['nue_oi_esfera'] . ' / ' . $row['nue_oi_cilindro'] . ' x ' . $row['nue_oi_eje']);
                                if ($grad_oi == ' /  x ') $grad_oi = '-';
                                
                                // Badge para tipo de consulta
                                $badge_consulta = '';
                                switch($row['tipo_consulta']) {
                                    case 'Nueva': $badge_consulta = 'badge-success'; break;
                                    case 'Control': $badge_consulta = 'badge-info'; break;
                                    case 'Revisión': $badge_consulta = 'badge-warning'; break;
                                    default: $badge_consulta = 'badge-secondary';
                                }
                        ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($row['nombre_cliente']); ?></strong></td>
                                    <td><?php echo $fecha; ?></td>
                                    <td><span class="badge <?php echo $badge_consulta; ?>"><?php echo htmlspecialchars($row['tipo_consulta']); ?></span></td>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($row['tipo_lente'] ?: '-'); ?></span></td>
                                    <td><?php echo $grad_od; ?></td>
                                    <td><?php echo $grad_oi; ?></td>
                                    <td><?php echo htmlspecialchars($row['nue_adicion'] ?: '-'); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="ver_historia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-modern btn-modern-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="eliminar_historia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-modern btn-modern-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta historia clínica?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No se encontraron historias clínicas</p>
                                    <a href="agregar_historia.php" class="btn btn-modern btn-modern-primary mt-3">
                                        <i class="fas fa-plus"></i> Crear primera historia
                                    </a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
