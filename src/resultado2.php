<?php require "../conexion.php";


$fecha= $_POST['calendario'];


$sql=$conexion->query(" SELECT * FROM ventas  where date(fecha)='$fecha'");
        if ($datos=$sql->fetch_object()) {        
            while ($fila = mysqli_fetch_array($sql)){
            echo "<tr><td>".$fila['id']."</td><td>".$fila['id_cliente']."</td><td>".$fila['total']."</td><td>".$fila['id_usuario']."</td><td>".$fila['fecha']."</td></tr>";
                echo "<br />";
          }
            }


?>