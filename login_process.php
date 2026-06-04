<?php
// ============================================================
// login_process.php — Procesa el formulario de inicio de sesión
// ============================================================

session_start();        // Inicia el sistema de sesiones de PHP
include('conexion.php'); // Trae la variable $conexion lista

// Verificamos que la solicitud venga de un formulario POST.
// Esto evita que alguien entre a esta URL directamente en el navegador.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recogemos los datos que el usuario escribió en el formulario
    $usuario_ingresado = $_POST['usuario_input'];
    $pass_ingresada    = $_POST['password'];

    // ============================================================
    // BUG CORREGIDO: SQL Injection
    // La versión original construía la query así:
    //   $sql = "SELECT * FROM Cuentas WHERE usuario = '$usuario_ingresado'";
    //
    // El problema: si alguien escribe esto en el campo usuario:
    //   ' OR '1'='1
    // La query quedaría:
    //   SELECT * FROM Cuentas WHERE usuario = '' OR '1'='1'
    // ... y '1'='1' siempre es verdadero, así que entraría sin contraseña.
    //
    // SOLUCIÓN: Prepared Statements (consultas preparadas).
    // El ? es un marcador de posición. MySQL recibe la query
    // y los datos por separado, así que nunca puede confundirlos.
    // ============================================================

    $stmt = $conexion->prepare("SELECT * FROM Cuentas WHERE usuario = ?");
    // bind_param: "s" significa que el parámetro es un String (texto)
    $stmt->bind_param("s", $usuario_ingresado);
    $stmt->execute();

    // get_result() convierte el resultado en un objeto que podemos leer
    $resultado  = $stmt->get_result();
    $usuario_bd = $resultado->fetch_assoc(); // Trae la fila como array asociativo

    // Verificamos dos cosas:
    // 1. Que el usuario exista en la base de datos ($usuario_bd no es null)
    // 2. Que la contraseña ingresada coincida con el hash guardado
    //    (password_verify compara el texto plano con el hash de password_hash)
    if ($usuario_bd && password_verify($pass_ingresada, $usuario_bd['password'])) {

        // Guardamos datos del usuario en la sesión.
        // La sesión es como una "memoria temporal" del servidor
        // que dura mientras el navegador esté abierto.
        $_SESSION['usuario_id']   = $usuario_bd['id'];
        $_SESSION['nombre_real']  = $usuario_bd['nombre'];
        $_SESSION['nick']         = $usuario_bd['usuario'];

        // Redirigimos al menú principal
        header("Location: menu.php");
        exit(); // Importante: siempre poner exit() después de header()
    } else {
        // Credenciales incorrectas: mostramos alerta y volvemos al login
        echo "<script>
                alert('Usuario o contraseña incorrectos.');
                window.location.href='index.html';
              </script>";
    }
}
?>
