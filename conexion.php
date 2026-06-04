<?php
// ============================================================
// conexion.php — Archivo de conexión a la base de datos
// ============================================================
// Este archivo se "incluye" en todos los demás PHP con:
//   include('conexion.php');
// Así no repetimos este código en cada archivo.
// ============================================================

$host = "localhost";       // Servidor donde corre MySQL (en XAMPP siempre es localhost)
$user = "root";            // Usuario de MySQL (en XAMPP el default es root)
$pass = "";                // Contraseña (en XAMPP viene vacía por default)
$db   = "proyecto_netflix"; // Nombre de la base de datos que creamos en el SQL

// mysqli_connect() intenta abrir la conexión con esos datos.
// Si falla, $conexion será false.
$conexion = mysqli_connect($host, $user, $pass, $db);

// Verificamos si la conexión falló
if (!$conexion) {
    // mysqli_connect_error() devuelve el mensaje de error de MySQL
    die("Error de conexión: " . mysqli_connect_error());
}

// Si llegamos aquí, la conexión fue exitosa.
// Los demás archivos que hagan include('conexion.php')
// ya tendrán la variable $conexion lista para usar.
?>
