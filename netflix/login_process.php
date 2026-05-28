<?php
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario_ingresado = $_POST['usuario_input']; 
    $pass_ingresada = $_POST['password'];

    $sql = "SELECT * FROM Cuentas WHERE usuario = '$usuario_ingresado'";
    $resultado = mysqli_query($conexion, $sql); 

    $usuario_bd = mysqli_fetch_assoc($resultado); 

    if ($usuario_bd && password_verify($pass_ingresada, $usuario_bd['password'])) {
        $_SESSION['usuario_id'] = $usuario_bd['id'];
        $_SESSION['nombre_real'] = $usuario_bd['nombre'];
        $_SESSION['nick'] = $usuario_bd['usuario'];
        
        header("Location: menu.php");
        exit();
    } else {
        echo "<script>
                alert('Usuario o contraseña incorrectos.');
                window.location.href='index.html';
              </script>";
    }
}
?>