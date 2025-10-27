<?php
require_once "../conexion.php";
session_start();

// Validar que existe sesión activa
if (!isset($_SESSION['idUser']) || empty($_SESSION['idUser'])) {
    echo json_encode(array('error' => 'No autorizado'));
    die();
}

$id_user = intval($_SESSION['idUser']); // Sanitizar ID de usuario

// Buscar clientes (autocomplete)
if (isset($_GET['q'])) {
    $datos = array();
    $nombre = mysqli_real_escape_string($conexion, $_GET['q']);
    $cliente = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre LIKE '%$nombre%' AND estado = 1 LIMIT 20");
    while ($row = mysqli_fetch_assoc($cliente)) {
        $data['id'] = intval($row['idcliente']);
        $data['label'] = $row['nombre'];
        $data['direccion'] = $row['direccion'];
        $data['telefono'] = $row['telefono'];
        $data['obrasocial'] = $row['obrasocial'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();

// Buscar productos (autocomplete)
} else if (isset($_GET['pro'])) {
    $datos = array();
    $nombre = mysqli_real_escape_string($conexion, $_GET['pro']);
    // Corregir lógica de WHERE
    $producto = mysqli_query($conexion, "SELECT * FROM producto WHERE estado = 1 AND existencia > 0 AND (codigo LIKE '%$nombre%' OR descripcion LIKE '%$nombre%') LIMIT 20");
    while ($row = mysqli_fetch_assoc($producto)) {
        $data['id'] = intval($row['codproducto']);
        $data['label'] = $row['codigo'] . ' - ' . $row['descripcion'];
        $data['value'] = $row['descripcion'];
        $data['precio'] = floatval($row['precio']);
        $data['existencia'] = intval($row['existencia']);
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();

// Obtener detalle de venta temporal
} else if (isset($_GET['detalle'])) {
    $datos = array();
    $detalle = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion, p.codigo FROM detalle_temp d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_usuario = $id_user");
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = intval($row['id']); // ID de detalle_temp para eliminar
        $data['codigo'] = $row['codigo']; // Código de producto
        $data['descripcion'] = $row['descripcion']; // Descripción del producto
        $data['cantidad'] = intval($row['cantidad']);
        $data['precio_venta'] = floatval($row['precio_venta']);
        $data['sub_total'] = floatval($row['precio_venta']) * floatval($row['cantidad']);
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();

// Eliminar detalle de venta temporal
} else if (isset($_GET['delete_detalle'])) {
    $id_detalle = intval($_GET['id']);
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id = $id_detalle AND id_usuario = $id_user");
    $datos = mysqli_fetch_assoc($verificar);
    
    if ($datos && $datos['cantidad'] > 1) {
        $cantidad = intval($datos['cantidad']) - 1;
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad WHERE id = $id_detalle AND id_usuario = $id_user");
        if ($query) {
            $msg = "restado";
        } else {
            $msg = "Error";
        }
    } else if ($datos) {
        $query = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id = $id_detalle AND id_usuario = $id_user");
        if ($query) {
            $msg = "ok";
        } else {
            $msg = "Error";
        }
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();

// Procesar venta completa
} else if (isset($_GET['procesarVenta'])) {
    // Sanitizar y validar inputs
    $id_cliente = intval($_GET['id']);
    $abona = floatval($_GET['abona']);
    $restoant = floatval($_GET['resto']);
    $descuento = floatval($_GET['descuento']);
    $obrasocial = isset($_GET['obrasocial']) ? floatval($_GET['obrasocial']) : 0;
    $metodo_pago = intval($_GET['metodo_pago']);
    $fecha = date("Y-m-d");

    // Validar que los valores sean positivos
    if ($abona < 0 || $descuento < 0 || $obrasocial < 0 || $metodo_pago < 1) {
        echo json_encode(array('mensaje' => 'error', 'detalle' => 'Valores inválidos'));
        die();
    }

    // Validar que existe cliente
    $verificar_cliente = mysqli_query($conexion, "SELECT idcliente FROM cliente WHERE idcliente = $id_cliente");
    if (mysqli_num_rows($verificar_cliente) == 0) {
        echo json_encode(array('mensaje' => 'error', 'detalle' => 'Cliente no válido'));
        die();
    }

    // Obtener totales
    $consulta = mysqli_query($conexion, "SELECT SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id_user");
    $result = mysqli_fetch_assoc($consulta);
    $total_pagar = floatval($result['total_pagar']);

    // Aplicar descuento y obra social
    $subtotal = $total_pagar - $obrasocial;
    $total = $subtotal * $descuento;

    // Validar que el abono no exceda el total
    if ($abona > $total) {
        echo json_encode(array('mensaje' => 'error', 'detalle' => 'El monto a abonar excede el total'));
        die();
    }

    $resto = $total - $abona;

    // INICIAR TRANSACCIÓN
    mysqli_begin_transaction($conexion);

    try {
        // 1. Insertar venta
        $insertar = mysqli_query($conexion, "INSERT INTO ventas(id_cliente, total, id_usuario, abona, resto, obrasocial, fecha, id_metodo) VALUES ($id_cliente, $total, $id_user, $abona, $resto, $obrasocial, '$fecha', $metodo_pago)");
        
        if (!$insertar) {
            throw new Exception("Error al insertar venta: " . mysqli_error($conexion));
        }

        $ultimoId = mysqli_insert_id($conexion);

        // 2. Insertar en ingresos
        $insertar_metodo = mysqli_query($conexion, "INSERT INTO ingresos(ingresos, fecha, id_venta, id_cliente, id_metodo) VALUES ($abona, '$fecha', $ultimoId, $id_cliente, $metodo_pago)");
        
        if (!$insertar_metodo) {
            throw new Exception("Error al insertar ingreso: " . mysqli_error($conexion));
        }

        // 3. Obtener detalles y graduaciones temporales
        $consultaDetalle = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $id_user");
        $consultaDetalle2 = mysqli_query($conexion, "SELECT * FROM graduaciones_temp WHERE id_usuario = $id_user");
        
        $postpagos_inserted = false; // Flag para controlar inserción en postpagos

        while ($row = mysqli_fetch_assoc($consultaDetalle)) {
            $id_producto = intval($row['id_producto']);
            $cantidad = intval($row['cantidad']);
            $precio_original = floatval($row['precio_venta']);
            $precio = $precio_original * $descuento;

            // VALIDAR STOCK antes de procesar
            $stockActual = mysqli_query($conexion, "SELECT existencia FROM producto WHERE codproducto = $id_producto");
            $stockNuevo = mysqli_fetch_assoc($stockActual);
            
            if (!$stockNuevo) {
                throw new Exception("Producto no encontrado");
            }

            $stock_disponible = intval($stockNuevo['existencia']);
            
            if ($stock_disponible < $cantidad) {
                throw new Exception("Stock insuficiente para el producto");
            }

            // Insertar detalle de venta (idcristal por defecto = 0)
            $insertarDet = mysqli_query($conexion, "INSERT INTO detalle_venta(id_producto, id_venta, cantidad, precio, precio_original, idcristal, abona, resto, obrasocial) VALUES ($id_producto, $ultimoId, $cantidad, $precio, $precio_original, 0, $abona, $resto, $obrasocial)");
            
            if (!$insertarDet) {
                throw new Exception(mysqli_error($conexion));
            }

            // Insertar en postpagos SOLO UNA VEZ por venta (no por cada producto)
            if (!$postpagos_inserted) {
                $postpagos = mysqli_query($conexion, "INSERT INTO postpagos(id_venta, abona, resto, precio, precio_original, id_cliente) VALUES ($ultimoId, $abona, $resto, $precio_original, $precio_original, $id_cliente)");
                
                if (!$postpagos) {
                    throw new Exception("Error al insertar postpagos: " . mysqli_error($conexion));
                }
                $postpagos_inserted = true;
            }

            // Actualizar stock
            $stockTotal = $stock_disponible - $cantidad;
            $stock = mysqli_query($conexion, "UPDATE producto SET existencia = $stockTotal WHERE codproducto = $id_producto");
            
            if (!$stock) {
                throw new Exception("Error al actualizar stock: " . mysqli_error($conexion));
            }

            // Ocultar producto automáticamente cuando el stock llegue a 0
            if ($stockTotal <= 0) {
                $ocultar = mysqli_query($conexion, "UPDATE producto SET estado = 0 WHERE codproducto = $id_producto");
                if (!$ocultar) {
                    // No lanzamos excepción aquí para no revertir la venta, solo registramos
                    error_log("Error al ocultar producto sin stock: " . mysqli_error($conexion));
                }
            }
        }

        // Insertar graduaciones
        while ($row2 = mysqli_fetch_assoc($consultaDetalle2)) {
            $ojolD1 = mysqli_real_escape_string($conexion, $row2['od_l_1']);
            $ojolD2 = mysqli_real_escape_string($conexion, $row2['od_l_2']);
            $ojolD3 = mysqli_real_escape_string($conexion, $row2['od_l_3']);
            $ojolI1 = mysqli_real_escape_string($conexion, $row2['oi_l_1']);
            $ojolI2 = mysqli_real_escape_string($conexion, $row2['oi_l_2']);
            $ojolI3 = mysqli_real_escape_string($conexion, $row2['oi_l_3']);
            $ojoD1 = mysqli_real_escape_string($conexion, $row2['od_c_1']);
            $ojoD2 = mysqli_real_escape_string($conexion, $row2['od_c_2']);
            $ojoD3 = mysqli_real_escape_string($conexion, $row2['od_c_3']);
            $ojoI1 = mysqli_real_escape_string($conexion, $row2['oi_c_1']);
            $ojoI2 = mysqli_real_escape_string($conexion, $row2['oi_c_2']);
            $ojoI3 = mysqli_real_escape_string($conexion, $row2['oi_c_3']);
            $add1 = mysqli_real_escape_string($conexion, $row2['addg']);
            $obs = mysqli_real_escape_string($conexion, $row2['obs']);

            $insedsd = mysqli_query($conexion, "INSERT INTO graduaciones(od_l_1, od_l_2, od_l_3, oi_l_1, oi_l_2, oi_l_3, od_c_1, od_c_2, od_c_3, oi_c_1, oi_c_2, oi_c_3, addg, id_venta, obs) VALUES ('$ojolD1', '$ojolD2', '$ojolD3', '$ojolI1', '$ojolI2', '$ojolI3', '$ojoD1', '$ojoD2', '$ojoD3', '$ojoI1', '$ojoI2', '$ojoI3', '$add1', $ultimoId, '$obs')");

            if (!$insedsd) {
                throw new Exception("Error al insertar graduaciones: " . mysqli_error($conexion));
            }
        }

        // Limpiar tablas temporales
        mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id_usuario = $id_user");
        mysqli_query($conexion, "DELETE FROM descuento WHERE id_usuario = $id_user");
        mysqli_query($conexion, "DELETE FROM graduaciones_temp WHERE id_usuario = $id_user");

        // CONFIRMAR TRANSACCIÓN
        mysqli_commit($conexion);

        $msg = array('id_cliente' => $id_cliente, 'id_venta' => $ultimoId, 'id' => $ultimoId);

    } catch (Exception $e) {
        // REVERTIR TRANSACCIÓN en caso de error
        mysqli_rollback($conexion);
        $msg = array('mensaje' => 'error', 'detalle' => $e->getMessage());
    }

    echo json_encode($msg);
    die();

// Registrar detalle en tabla temporal
} else if (isset($_POST['action'])) {
    $id = intval($_POST['id']);
    $cant = intval($_POST['cant']);
    $precio = floatval($_POST['precio']);
    $total = $precio * $cant;
    
    // Validar que existe el producto y tiene stock
    $verificar_producto = mysqli_query($conexion, "SELECT existencia FROM producto WHERE codproducto = $id AND estado = 1");
    $producto_data = mysqli_fetch_assoc($verificar_producto);
    
    if (!$producto_data) {
        echo json_encode("producto_no_existe");
        die();
    }
    
    $existencia = intval($producto_data['existencia']);
    
    // Verificar si el producto ya está en el carrito
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_producto = $id AND id_usuario = $id_user");
    $result = mysqli_num_rows($verificar);
    $datos = mysqli_fetch_assoc($verificar);

    if ($result > 0) {
        // Actualizar cantidad existente
        $cantidad_actual = intval($datos['cantidad']);
        $nueva_cantidad = $cantidad_actual + $cant;
        
        // Validar stock
        if ($nueva_cantidad > $existencia) {
            echo json_encode("stock_insuficiente");
            die();
        }
        
        $total_precio = $nueva_cantidad * $precio;
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $nueva_cantidad, total = $total_precio WHERE id_producto = $id AND id_usuario = $id_user");
        
        if ($query) {
            $msg = "actualizado";
        } else {
            $msg = "Error al ingresar";
        }
    } else {
        // Validar stock disponible
        if ($cant > $existencia) {
            echo json_encode("stock_insuficiente");
            die();
        }
        
        // Insertar nuevo producto
        $query = mysqli_query($conexion, "INSERT INTO detalle_temp(id_usuario, id_producto, cantidad, precio_venta, total) VALUES ($id_user, $id, $cant, $precio, $total)");
        
        if ($query) {
            $msg = "registrado";
        } else {
            $msg = "Error al ingresar";
        }
    }
    
    echo json_encode($msg);
    die();

// Cambiar contraseña
} else if (isset($_POST['cambio'])) {
    if (empty($_POST['actual']) || empty($_POST['nueva'])) {
        $msg = 'Los campos estan vacios';
    } else {
        $actual = md5($_POST['actual']);
        $nueva = md5($_POST['nueva']);
        $consulta = mysqli_query($conexion, "SELECT * FROM usuario WHERE clave = '$actual' AND idusuario = $id_user");
        $result = mysqli_num_rows($consulta);
        
        if ($result == 1) {
            $query = mysqli_query($conexion, "UPDATE usuario SET clave = '$nueva' WHERE idusuario = $id_user");
            if ($query) {
                $msg = 'ok';
            } else {
                $msg = 'error';
            }
        } else {
            $msg = 'dif';
        }
    }
    
    echo $msg;
    die();
}
