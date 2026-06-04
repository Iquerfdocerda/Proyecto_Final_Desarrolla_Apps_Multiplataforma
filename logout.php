<?php
// logout.php: Cierra la sesión del usuario

session_start();   

// Se eliminan los datos de la sesion
session_destroy();

// Redirir al login
header("Location: index.html");
exit();
?>
