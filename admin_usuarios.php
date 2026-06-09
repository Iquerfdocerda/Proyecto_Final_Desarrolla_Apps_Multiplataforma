<?php
// admin_usuarios.php: CRUD completo de la tabla Cuentas (usuarios)
// SOLO accesible para admins

session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

if ($_SESSION['rol'] !== 'admin') {
    header("Location: menu.php");
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'crear') {
    $nombre          = trim($_POST['nombre']);
    $apellido        = trim($_POST['apellido']);
    $usuario         = trim($_POST['usuario']);
    $edad            = intval($_POST['edad']);
    $email           = trim($_POST['email']);
    $pass            = $_POST['password'];
    $rol             = in_array($_POST['rol'], ['admin','usuario']) ? $_POST['rol'] : 'usuario';
    $pass_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare(
        "INSERT INTO Cuentas (nombre, apellido, usuario, edad, email, password, rol)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssisss", $nombre, $apellido, $usuario, $edad, $email, $pass_encriptada, $rol);
    $mensaje = $stmt->execute()
        ? "✅ Usuario «{$usuario}» creado correctamente."
        : "❌ Error: ese usuario o email ya existe.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    $id       = intval($_POST['id']);
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $usuario  = trim($_POST['usuario']);
    $edad     = intval($_POST['edad']);
    $email    = trim($_POST['email']);
    $rol      = in_array($_POST['rol'], ['admin','usuario']) ? $_POST['rol'] : 'usuario';

    if (!empty($_POST['password'])) {
        $pass_encriptada = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conexion->prepare(
            "UPDATE Cuentas SET nombre=?, apellido=?, usuario=?, edad=?, email=?, password=?, rol=? WHERE id=?"
        );
        $stmt->bind_param("sssisssi", $nombre, $apellido, $usuario, $edad, $email, $pass_encriptada, $rol, $id);
    } else {

        $stmt = $conexion->prepare(
            "UPDATE Cuentas SET nombre=?, apellido=?, usuario=?, edad=?, email=?, rol=? WHERE id=?"
        );
        $stmt->bind_param("sssissi", $nombre, $apellido, $usuario, $edad, $email, $rol, $id);
    }
    $mensaje = $stmt->execute() ? "✅ Usuario actualizado." : "❌ Error al actualizar.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'eliminar') {
    $id = intval($_POST['id']);

    if ($id === intval($_SESSION['usuario_id'])) {
        $mensaje = "❌ No puedes eliminar tu propia cuenta.";
    } else {
        $stmt = $conexion->prepare("DELETE FROM Cuentas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $mensaje = $stmt->execute() ? "🗑️ Usuario eliminado." : "❌ Error al eliminar.";
    }
}

$usuarios = mysqli_query($conexion, "SELECT id, nombre, apellido, usuario, edad, email, rol FROM Cuentas ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix - Gestión de Usuarios</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #141414; color: white; font-family: Arial, sans-serif; min-height: 100vh; }

        /* HEADER */
        .header { display: flex; align-items: center; justify-content: space-between; background: #000; padding: 14px 40px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.8); }
        .logo { color: #E50914; font-size: 26px; font-weight: bold; letter-spacing: 2px; }
        .nav-links { display: flex; gap: 16px; align-items: center; }
        .nav-links a { color: #aaa; text-decoration: none; font-size: 14px; }
        .nav-links a:hover { color: white; }
        .badge-admin { font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; background: #E50914; color: white; }

        /* MENSAJE */
        .mensaje { text-align: center; padding: 12px 20px; margin: 16px 40px 0; border-radius: 4px; font-size: 15px; background: #1a3a1a; border-left: 4px solid #46d369; }
        .mensaje.error { background: #3a1a1a; border-left-color: #E50914; }

        /* ENCABEZADO DE SECCIÓN */
        .seccion-header { display: flex; justify-content: space-between; align-items: center; padding: 28px 40px 16px; }
        .seccion-header h2 { font-size: 20px; color: #ccc; }
        .btn-nuevo { background: #E50914; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; }
        .btn-nuevo:hover { background: #b9090b; }

        /* TABLA DE USUARIOS */
        .tabla-wrapper { padding: 0 40px 60px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead tr { background: #1a1a1a; }
        th { padding: 12px 14px; text-align: left; color: #aaa; font-weight: bold; border-bottom: 2px solid #333; }
        td { padding: 12px 14px; border-bottom: 1px solid #222; vertical-align: middle; }
        tbody tr:hover { background: #1c1c1c; }

        /* Badge de rol dentro de la tabla */
        .rol-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .rol-admin   { background: #E50914; color: white; }
        .rol-usuario { background: #333;    color: #aaa;  }

        /* Botones de acción en la tabla */
        .btn-tabla-editar   { background: #1a6b3c; color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-right: 6px; }
        .btn-tabla-editar:hover { background: #46d369; color: #000; }
        .btn-tabla-eliminar { background: #3a1a1a; color: #E50914; border: 1px solid #E50914; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-tabla-eliminar:hover { background: #E50914; color: white; }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 2000; justify-content: center; align-items: center; }
        .modal-overlay.activo { display: flex; }
        .modal { background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 36px; width: 100%; max-width: 500px; position: relative; max-height: 90vh; overflow-y: auto; }
        .modal h2 { color: #E50914; margin-bottom: 24px; font-size: 22px; }
        .modal label { display: block; color: #aaa; font-size: 13px; margin-bottom: 5px; margin-top: 14px; }
        .modal input, .modal select { width: 100%; padding: 10px 12px; background: #2a2a2a; border: 1px solid #444; border-radius: 4px; color: white; font-size: 14px; }
        .modal input:focus, .modal select:focus { outline: none; border-color: #E50914; }
        .modal-btns { display: flex; gap: 12px; margin-top: 24px; }
        .btn-guardar { flex: 1; padding: 12px; background: #E50914; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 15px; }
        .btn-guardar:hover { background: #b9090b; }
        .btn-cancelar { flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; }
        .btn-cancelar:hover { background: #444; }
        .btn-cerrar { position: absolute; top: 12px; right: 16px; background: none; border: none; color: #888; font-size: 24px; cursor: pointer; }
        .btn-cerrar:hover { color: white; }
        .hint { font-size: 12px; color: #666; margin-top: 4px; }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">NETFLIX</div>
    <div class="nav-links">
        <span class="badge-admin">Admin</span>
        <span style="color:#ccc; font-size:14px;">Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre_real']); ?></strong></span>
        <a href="menu.php">← Catálogo</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>

<?php if ($mensaje): ?>
    <div class="mensaje <?php echo str_starts_with($mensaje, '❌') ? 'error' : ''; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="seccion-header">
    <h2>👥 Gestión de Usuarios</h2>
    <button class="btn-nuevo" onclick="abrirModalCrear()">+ Nuevo Usuario</button>
</div>

<div class="tabla-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Usuario</th>
                <th>Edad</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                <td><?php echo htmlspecialchars($u['apellido']); ?></td>
                <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                <td><?php echo $u['edad']; ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>

                    <span class="rol-badge <?php echo $u['rol'] === 'admin' ? 'rol-admin' : 'rol-usuario'; ?>">
                        <?php echo $u['rol']; ?>
                    </span>
                </td>
                <td>
                    <button class="btn-tabla-editar" onclick="abrirModalEditar(
                        <?php echo $u['id']; ?>,
                        <?php echo json_encode($u['nombre']); ?>,
                        <?php echo json_encode($u['apellido']); ?>,
                        <?php echo json_encode($u['usuario']); ?>,
                        <?php echo $u['edad']; ?>,
                        <?php echo json_encode($u['email']); ?>,
                        <?php echo json_encode($u['rol']); ?>
                    )">✏️ Editar</button>

                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>

                        <button class="btn-tabla-eliminar" onclick="confirmarEliminar(
                            <?php echo $u['id']; ?>,
                            <?php echo json_encode($u['usuario']); ?>
                        )">🗑️ Eliminar</button>
                    <?php else: ?>
                        <span style="color:#555; font-size:12px;">(tu cuenta)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="modal-crear">
    <div class="modal">
        <button class="btn-cerrar" onclick="cerrarModal('modal-crear')">×</button>
        <h2>➕ Nuevo Usuario</h2>
        <form method="POST" action="admin_usuarios.php">
            <input type="hidden" name="accion" value="crear">
            <label>Nombre *</label>
            <input type="text" name="nombre" required>
            <label>Apellido *</label>
            <input type="text" name="apellido" required>
            <label>Usuario (nick) *</label>
            <input type="text" name="usuario" required>
            <label>Edad *</label>
            <input type="number" name="edad" min="13" max="120" required>
            <label>Email *</label>
            <input type="email" name="email" required>
            <label>Contraseña *</label>
            <input type="password" name="password" required>
            <label>Rol *</label>
            <select name="rol" required>
                <option value="usuario">Usuario (solo lectura)</option>
                <option value="admin">Administrador</option>
            </select>
            <div class="modal-btns">
                <button type="submit" class="btn-guardar">Crear</button>
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modal-crear')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-editar">
    <div class="modal">
        <button class="btn-cerrar" onclick="cerrarModal('modal-editar')">×</button>
        <h2>✏️ Editar Usuario</h2>
        <form method="POST" action="admin_usuarios.php">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" id="editar-id">
            <label>Nombre *</label>
            <input type="text" name="nombre" id="editar-nombre" required>
            <label>Apellido *</label>
            <input type="text" name="apellido" id="editar-apellido" required>
            <label>Usuario (nick) *</label>
            <input type="text" name="usuario" id="editar-usuario" required>
            <label>Edad *</label>
            <input type="number" name="edad" id="editar-edad" min="13" max="120" required>
            <label>Email *</label>
            <input type="email" name="email" id="editar-email" required>
            <label>Nueva Contraseña</label>
            <input type="password" name="password" placeholder="Dejar vacío para no cambiar">
            <p class="hint">Si dejas este campo vacío, la contraseña actual se conserva.</p>
            <label>Rol *</label>
            <select name="rol" id="editar-rol" required>
                <option value="usuario">Usuario (solo lectura)</option>
                <option value="admin">Administrador</option>
            </select>
            <div class="modal-btns">
                <button type="submit" class="btn-guardar">Guardar cambios</button>
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modal-editar')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="admin_usuarios.php" id="form-eliminar" style="display:none">
    <input type="hidden" name="accion" value="eliminar">
    <input type="hidden" name="id" id="eliminar-id">
</form>

<script>
function abrirModalCrear() {
    document.getElementById('modal-crear').classList.add('activo');
}

function abrirModalEditar(id, nombre, apellido, usuario, edad, email, rol) {
    document.getElementById('editar-id').value       = id;
    document.getElementById('editar-nombre').value   = nombre;
    document.getElementById('editar-apellido').value = apellido;
    document.getElementById('editar-usuario').value  = usuario;
    document.getElementById('editar-edad').value     = edad;
    document.getElementById('editar-email').value    = email;

    const sel = document.getElementById('editar-rol');
    for (let opt of sel.options) {
        if (opt.value === rol) { opt.selected = true; break; }
    }
    document.getElementById('modal-editar').classList.add('activo');
}

function cerrarModal(id) {
    document.getElementById(id).classList.remove('activo');
}

function confirmarEliminar(id, usuario) {
    if (confirm(`¿Eliminar al usuario "${usuario}"?\nEsta acción no se puede deshacer.`)) {
        document.getElementById('eliminar-id').value = id;
        document.getElementById('form-eliminar').submit();
    }
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('activo');
    });
});
</script>

</body>
</html>
