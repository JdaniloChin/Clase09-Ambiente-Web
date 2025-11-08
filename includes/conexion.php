<?php
//Control conexion a la base de datos
//Activar reporte de errores
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $host = 'localhost';
    $user = 'root';
    $pass = 'Jdcc7206.';
    $database = 'tienda_app';

    $mysqli = new mysqli($host,$user,$pass,$database);
    if($mysqli->connect_error){
        echo "<div class='alert alert-danger'>Error en la conexion a la base de datos</div>";
    }else {
        $mysqli->set_charset('utf8mb4');
    }
?>