<?php
// register_process.php: Procesa el formulario de registro

include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recogemos todos los campos del formulario de registro
    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario  = $_POST['usuario'];
    $edad     = $_POST['edad'];
    $email    = $_POST['email'];
    $pass     = $_POST['password'];

    // Se encripta la contraseña
    $pass_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare(
        "INSERT INTO Cuentas (nombre, apellido, usuario, edad, email, password)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssiss", $nombre, $apellido, $usuario, $edad, $email, $pass_encriptada);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Cuenta creada! Ya puedes iniciar sesión.');
                window.location.href='index.html';
              </script>";
    } else {
        // Si falla:
        echo "<script>
                alert('Error: ese usuario o email ya está registrado.');
                window.history.back();
              </script>";
    }
}
?>
