<?php
// ============================================================
// logout.php — Cierra la sesión del usuario
// ============================================================

session_start();    // Necesario para poder acceder a la sesión y destruirla

// session_destroy() elimina todos los datos de la sesión en el servidor.
// Después de esto, $_SESSION queda vacío en la próxima solicitud.
session_destroy();

// Redirigimos al login
header("Location: index.html");
exit();
?>
