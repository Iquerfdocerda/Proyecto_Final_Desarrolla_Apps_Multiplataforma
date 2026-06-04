<?php
// login_process.php: Procesa el formulario de inicio de sesión

session_start();       
include('conexion.php'); 

// Verificamos que la solicitud venga de un formulario POST para que nadie entre a la URL directamente del navegador
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Se recogen los datos que el usuario escribió en el formulario
    $usuario_ingresado = $_POST['usuario_input'];
    $pass_ingresada    = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM Cuentas WHERE usuario = ?");
    // bind_param: "s" significa que el parámetro es un String (texto)
    $stmt->bind_param("s", $usuario_ingresado);
    $stmt->execute();

    // get_result() convierte el resultado en un objeto que podemos leer
    $resultado  = $stmt->get_result();
    $usuario_bd = $resultado->fetch_assoc(); // Trae la fila como array asociativo

    // Verificamos que el usurio exista y la contraseña coincida
    if ($usuario_bd && password_verify($pass_ingresada, $usuario_bd['password'])) {

        // Se guardan los datos del usuario en la sesión.
        $_SESSION['usuario_id']   = $usuario_bd['id'];
        $_SESSION['nombre_real']  = $usuario_bd['nombre'];
        $_SESSION['nick']         = $usuario_bd['usuario'];

        // Redirigimos al menú principal
        header("Location: menu.php");
        exit();
    } else {
        // Si no coincide usuario y/o contraseña: mostramos alerta y volvemos al login
        echo "<script>
                alert('Usuario y/o contraseña incorrectos.');
                window.location.href='index.html';
              </script>";
    }
}
?>
