<?php include_once "includes/header.php";
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "idcristal";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h4 class="text-center">ID Cristales</h4>
        </div>
    </div>
</div>

            <form method="post" id="form_cristal">
                <div class="row justify-content-center">
                    <div class="col-md-2 text-center">
                        <div class="card">
                            <div class="card-header">
                                Buscar ID Venta
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="idventa" class="form-control" type="number" name="idventa" placeholder="Ingresá el Id de la venta">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="card">
                            <div class="card-header">
                                Colocar ID Cristal
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="idcristal" class="form-control" type="number" name="idcristal" placeholder="Ingresá el Id de cristales">
                                </div>
                            </div>
                        </div>
                    </div>                  
                </div>
            </form>
            <br> 
<div class="row justify-content-center">
    <input type="button" class="btn btn-primary" value="Colocar Id Cristal" id="guardar_cristal" name="guardar_cristal" onclick=""></input> 
</div>
<div id="div_cristal"></div>

<br>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h4 class="text-center">Post Pagos</h4><br>
        </div>
    </div>
</div>
            <form method="post" id="form_venta">
                <div class="row justify-content-center">
                    <div class="col-md-2 text-center">
                        <div class="card">
                            <div class="card-header">
                                Buscar ID Venta
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="idventa" class="form-control" type="number" name="idventa" placeholder="Ingresá el Id de la venta">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="card">
                            <div class="card-header">
                                Colocar cantidad a Abonar
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="idabona" class="form-control" type="number" name="idabona" placeholder="Ingresá el monto">
                                </div>
                            </div>
                        </div>
                    </div>                  
                </div>
            </form>
            <br> 
<div class="row justify-content-center">
    <input type="button" class="btn btn-primary" value="Buscar Venta" id="buscar_venta" name="buscar_venta" onclick=""></input> 
</div>
<div id="div_venta"></div>
<br>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h4 class="text-center">Anular Venta</h4><br>
        </div>
    </div>
</div>
            <form method="post" id="form_anular">
                <div class="row justify-content-center">
                    <div class="col-md-2 text-center">
                        <div class="card">
                            <div class="card-header">
                                Buscar ID Venta
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input id="idanular" class="form-control" type="number" name="idanular" placeholder="Ingresá el Id de la venta">
                                </div>
                            </div>
                        </div>
                    </div>        
                </div>
            </form>
            <br> 
<div class="row justify-content-center">
    <input type="button" class="btn btn-primary" value="Anular Venta" id="anular_venta" name="anular_venta" onclick=""></input> 
</div>
<div id="div_anular"></div>




<?php include_once "includes/footer.php"; ?>