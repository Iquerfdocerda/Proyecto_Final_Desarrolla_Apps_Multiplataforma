<?php
// register_process.php: Procesa el formulario de registro

include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario  = $_POST['usuario'];
    $edad     = $_POST['edad'];
    $email    = $_POST['email'];
    $pass     = $_POST['password'];

    $rol_recibido = $_POST['rol'];
    if (!in_array($rol_recibido, ['admin', 'usuario'])) {
        echo "<script>
                alert('Rol no válido.');
                window.history.back();
              </script>";
        exit();
    }
    $rol = $rol_recibido;

    $pass_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare(
        "INSERT INTO Cuentas (nombre, apellido, usuario, edad, email, password, rol)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssisss", $nombre, $apellido, $usuario, $edad, $email, $pass_encriptada, $rol);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Cuenta creada! Ya puedes iniciar sesión.');
                window.location.href='index.html';
              </script>";
    } else {
        echo "<script>
                alert('Error: ese usuario o email ya está registrado.');
                window.history.back();
              </script>";
    }
}
?>
