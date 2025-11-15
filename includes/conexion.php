<?php
// includes/conexion.php
$host = "localhost";
$user = "root";
$pass = "";              // XAMPP por defecto
$db   = "tienda_app";    // BD importada del SQL

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error"=>"DB connection failed"]));
}
$conn->set_charset("utf8mb4");
