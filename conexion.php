<?php
// conexion.php — Archivo de conexión a la base de datos

$host = "localhost";      
$user = "root";            
$pass = "";                
$db   = "proyecto_netflix"; 

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    //Se devuelve el mensaje de error de MySQL.
    die("Error de conexión: " . mysqli_connect_error());
}

// Si llegamos aquí, la conexión fue exitosa.
// Los demás archivos que hagan include('conexion.php')
// ya tendrán la variable $conexion lista para usar.
?>
