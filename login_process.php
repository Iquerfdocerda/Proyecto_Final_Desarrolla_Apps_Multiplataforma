<?php
// login_process.php: Procesa el formulario de inicio de sesión

session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario_ingresado = $_POST['usuario_input'];
    $pass_ingresada    = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM Cuentas WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_ingresado);
    $stmt->execute();
    $resultado  = $stmt->get_result();
    $usuario_bd = $resultado->fetch_assoc();

    if ($usuario_bd && password_verify($pass_ingresada, $usuario_bd['password'])) {

        $_SESSION['usuario_id']  = $usuario_bd['id'];
        $_SESSION['nombre_real'] = $usuario_bd['nombre'];
        $_SESSION['nick']        = $usuario_bd['usuario'];
        $_SESSION['rol']         = $usuario_bd['rol']; 

        header("Location: menu.php");
        exit();
    } else {
        echo "<script>
                alert('Usuario y/o contraseña incorrectos.');
                window.location.href='index.html';
              </script>";
    }
}
?>
