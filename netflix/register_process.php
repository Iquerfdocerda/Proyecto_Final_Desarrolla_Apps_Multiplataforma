<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario  = $_POST['usuario'];
    $edad     = $_POST['edad'];
    $email    = $_POST['email'];
    $pass     = $_POST['password'];

    $pass_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Cuentas (nombre, apellido, usuario, edad, email, password) 
            VALUES ('$nombre', '$apellido', '$usuario', '$edad', '$email', '$pass_encriptada')";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>
                alert('¡Cuenta creada! Ya puedes iniciar sesión con tu email.');
                window.location.href='index.html';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>