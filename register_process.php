<?php
// ============================================================
// register_process.php — Procesa el formulario de registro
// ============================================================

include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recogemos todos los campos del formulario de registro
    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario  = $_POST['usuario'];
    $edad     = $_POST['edad'];
    $email    = $_POST['email'];
    $pass     = $_POST['password'];

    // password_hash() convierte la contraseña en texto plano
    // a un hash seguro. Por ejemplo "miPass123" se convierte en algo como:
    // $2y$10$abcdefghijklmnopqrstuuVwXyZ0123456789ABCDEFGHIJKLMNOP
    // PASSWORD_DEFAULT usa el algoritmo bcrypt, el más recomendado hoy.
    // NUNCA guardamos contraseñas en texto plano en la base de datos.
    $pass_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    // ============================================================
    // BUG CORREGIDO: SQL Injection (mismo problema que en login)
    // Usamos Prepared Statement con 6 parámetros (6 signos de ?)
    // "sssiis" = string, string, string, integer, integer, string
    // ============================================================
    $stmt = $conexion->prepare(
        "INSERT INTO Cuentas (nombre, apellido, usuario, edad, email, password)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    // bind_param: "sssiss" → s=string, i=integer
    // El orden debe coincidir exactamente con los ? de arriba
    $stmt->bind_param("sssiss", $nombre, $apellido, $usuario, $edad, $email, $pass_encriptada);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Cuenta creada! Ya puedes iniciar sesión.');
                window.location.href='index.html';
              </script>";
    } else {
        // Si falla (por ejemplo, el usuario o email ya existe)
        echo "<script>
                alert('Error: ese usuario o email ya está registrado.');
                window.history.back();
              </script>";
    }
}
?>
