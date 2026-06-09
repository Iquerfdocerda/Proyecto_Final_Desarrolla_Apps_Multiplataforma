<?php
// menu.php: Catálogo principal + CRUD de películas (solo admins)
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}
$es_admin = ($_SESSION['rol'] === 'admin');

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    if (!$es_admin) die("Acceso denegado.");

    $titulo      = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $genero      = trim($_POST['genero']);
    $anio        = intval($_POST['anio']);
    $portada     = trim($_POST['portada']);
    if ($portada === '') $portada = 'img/placeholder.png';

    $stmt = $conexion->prepare(
        "INSERT INTO peliculas (titulo, descripcion, genero, anio, portada) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssis", $titulo, $descripcion, $genero, $anio, $portada);
    $mensaje = $stmt->execute() ? "✅ «{$titulo}» agregada al catálogo." : "❌ Error al agregar.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    if (!$es_admin) die("Acceso denegado.");

    $id          = intval($_POST['id']);
    $titulo      = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $genero      = trim($_POST['genero']);
    $anio        = intval($_POST['anio']);
    $portada     = trim($_POST['portada']);
    if ($portada === '') $portada = 'img/placeholder.png';

    $stmt = $conexion->prepare(
        "UPDATE peliculas SET titulo=?, descripcion=?, genero=?, anio=?, portada=? WHERE id=?"
    );
    $stmt->bind_param("sssisi", $titulo, $descripcion, $genero, $anio, $portada, $id);
    $mensaje = $stmt->execute() ? "✅ «{$titulo}» actualizada." : "❌ Error al actualizar.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    if (!$es_admin) die("Acceso denegado.");

    $id   = intval($_POST['id']);
    $stmt = $conexion->prepare("DELETE FROM peliculas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $mensaje = $stmt->execute() ? "🗑️ Película eliminada." : "❌ Error al eliminar.";
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
if ($busqueda !== '') {
    $stmt = $conexion->prepare("SELECT * FROM peliculas WHERE titulo LIKE ? ORDER BY id DESC");
    $termino = "%{$busqueda}%";
    $stmt->bind_param("s", $termino);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = mysqli_query($conexion, "SELECT * FROM peliculas ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix - Catálogo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #141414; color: white; font-family: Arial, sans-serif; min-height: 100vh; }

        /* HEADER */
        .header {
            display: flex; align-items: center; justify-content: space-between;
            background: #000; padding: 14px 40px;
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.8);
        }
        .logo { color: #E50914; font-size: 26px; font-weight: bold; letter-spacing: 2px; }
        .buscador { flex: 1; display: flex; justify-content: center; }
        .buscador form { display: flex; background: #222; border-radius: 4px; overflow: hidden; border: 1px solid #444; }
        .buscador input { padding: 8px 14px; border: none; outline: none; background: transparent; color: white; width: 260px; font-size: 14px; }
        .buscador button { background: #E50914; border: none; color: white; padding: 8px 16px; cursor: pointer; font-size: 14px; }
        .buscador button:hover { background: #b9090b; }
        .usuario { display: flex; align-items: center; gap: 16px; font-size: 14px; }
        .usuario span { color: #ccc; }
        .usuario a { color: #aaa; text-decoration: none; }
        .usuario a:hover { color: white; }

        /* Etiqueta de rol visible en el header */
        .badge-rol {
            font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .badge-admin   { background: #E50914; color: white; }
        .badge-usuario { background: #333;    color: #aaa;  }

        /* BOTONES DE ACCIÓN */
        .btn-agregar { background: #E50914; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; }
        .btn-agregar:hover { background: #b9090b; }
        .btn-admin-usuarios { background: #1a6b3c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-admin-usuarios:hover { background: #46d369; color: #000; }

        /* MENSAJE */
        .mensaje { text-align: center; padding: 12px 20px; margin: 16px 40px 0; border-radius: 4px; font-size: 15px; background: #1a3a1a; border-left: 4px solid #46d369; }
        .mensaje.error { background: #3a1a1a; border-left-color: #E50914; }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 2000; justify-content: center; align-items: center; }
        .modal-overlay.activo { display: flex; }
        .modal { background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 36px; width: 100%; max-width: 500px; position: relative; }
        .modal h2 { color: #E50914; margin-bottom: 24px; font-size: 22px; }
        .modal label { display: block; color: #aaa; font-size: 13px; margin-bottom: 5px; margin-top: 14px; }
        .modal input, .modal textarea, .modal select { width: 100%; padding: 10px 12px; background: #2a2a2a; border: 1px solid #444; border-radius: 4px; color: white; font-size: 14px; }
        .modal textarea { resize: vertical; min-height: 80px; }
        .modal input:focus, .modal textarea:focus, .modal select:focus { outline: none; border-color: #E50914; }
        .modal-btns { display: flex; gap: 12px; margin-top: 24px; }
        .btn-guardar { flex: 1; padding: 12px; background: #E50914; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 15px; }
        .btn-guardar:hover { background: #b9090b; }
        .btn-cancelar { flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; }
        .btn-cancelar:hover { background: #444; }
        .btn-cerrar { position: absolute; top: 12px; right: 16px; background: none; border: none; color: #888; font-size: 24px; cursor: pointer; }
        .btn-cerrar:hover { color: white; }

        /* CATÁLOGO */
        .catalogo-header { display: flex; justify-content: space-between; align-items: center; padding: 28px 40px 10px; gap: 12px; }
        .catalogo-header h2 { font-size: 20px; color: #ccc; }
        .catalogo-acciones { display: flex; gap: 10px; }
        .contenedor-peliculas { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 18px; padding: 20px 40px 60px; }
        .pelicula-card { background: #1c1c1c; border-radius: 6px; overflow: hidden; transition: transform 0.25s ease, box-shadow 0.25s ease; cursor: pointer; position: relative; }
        .pelicula-card:hover { transform: scale(1.06); z-index: 10; box-shadow: 0 8px 25px rgba(0,0,0,0.7); }
        .pelicula-card img { width: 100%; aspect-ratio: 2 / 3; object-fit: cover; display: block; background: #2a2a2a; }
        .info { padding: 10px; }
        .info h4 { font-size: 0.9rem; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .info .genero { font-size: 0.75rem; color: #46d369; font-weight: bold; }
        .info .anio   { font-size: 0.75rem; color: #888; }

        /* Botones CRUD sobre la tarjeta: solo visibles para admin */
        .crud-btns { position: absolute; top: 8px; right: 8px; display: flex; flex-direction: column; gap: 6px; opacity: 0; transition: opacity 0.2s; }
        .pelicula-card:hover .crud-btns { opacity: 1; }
        .btn-editar, .btn-eliminar { border: none; border-radius: 4px; padding: 5px 10px; font-size: 12px; font-weight: bold; cursor: pointer; }
        .btn-editar   { background: rgba(70,211,105,0.9); color: #000; }
        .btn-editar:hover { background: #46d369; }
        .btn-eliminar { background: rgba(229,9,20,0.9); color: white; }
        .btn-eliminar:hover { background: #E50914; }

        .sin-resultados { grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #555; font-size: 18px; }
        #preview-portada { margin-top: 10px; width: 80px; height: 110px; object-fit: cover; border-radius: 4px; border: 1px solid #444; display: none; }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">NETFLIX</div>

    <div class="buscador">
        <form action="menu.php" method="GET">
            <input type="text" name="buscar" placeholder="Buscar película o serie..."
                   value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">🔍</button>
        </form>
    </div>

    <div class="usuario">
        <!-- Mostramos el rol como una etiqueta visual en el header -->
        <span class="badge-rol <?php echo $es_admin ? 'badge-admin' : 'badge-usuario'; ?>">
            <?php echo $es_admin ? 'Admin' : 'Usuario'; ?>
        </span>
        <span>Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre_real']); ?></strong></span>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>

<?php if ($mensaje): ?>
    <div class="mensaje <?php echo str_starts_with($mensaje, '❌') ? 'error' : ''; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="catalogo-header">
    <h2>
        <?php if ($busqueda): ?>
            Resultados para: "<?php echo htmlspecialchars($busqueda); ?>"
            &nbsp;<a href="menu.php" style="font-size:13px; color:#888;">Ver todo</a>
        <?php else: ?>
            Catálogo completo
        <?php endif; ?>
    </h2>

    <div class="catalogo-acciones">
        <?php if ($es_admin): ?>

            <a href="admin_usuarios.php" class="btn-admin-usuarios">👥 Gestionar Usuarios</a>
            <button class="btn-agregar" onclick="abrirModalCrear()">+ Agregar título</button>
        <?php endif; ?>
    </div>
</div>

<main class="contenedor-peliculas">
    <?php if (mysqli_num_rows($resultado) > 0):
        while ($row = mysqli_fetch_assoc($resultado)): ?>

        <div class="pelicula-card">
            <img src="<?php echo htmlspecialchars($row['portada']); ?>"
                 alt="<?php echo htmlspecialchars($row['titulo']); ?>"
                 onerror="this.src='img/placeholder.png'">

            <div class="info">
                <h4 title="<?php echo htmlspecialchars($row['titulo']); ?>">
                    <?php echo htmlspecialchars($row['titulo']); ?>
                </h4>
                <div class="genero"><?php echo htmlspecialchars($row['genero']); ?></div>
                <div class="anio"><?php echo $row['anio']; ?></div>
            </div>

            <?php if ($es_admin): ?>
            
            <div class="crud-btns">
                <button class="btn-editar" onclick="abrirModalEditar(
                    <?php echo $row['id']; ?>,
                    <?php echo json_encode($row['titulo']); ?>,
                    <?php echo json_encode($row['descripcion']); ?>,
                    <?php echo json_encode($row['genero']); ?>,
                    <?php echo $row['anio']; ?>,
                    <?php echo json_encode($row['portada']); ?>
                )">✏️ Editar</button>

                <button class="btn-eliminar" onclick="confirmarEliminar(
                    <?php echo $row['id']; ?>,
                    <?php echo json_encode($row['titulo']); ?>
                )">🗑️ Eliminar</button>
            </div>
            <?php endif; ?>
        </div>

    <?php endwhile;
    else: ?>
        <div class="sin-resultados">
            <p>No se encontraron títulos<?php echo $busqueda ? " para \"$busqueda\"" : ''; ?>.</p>
        </div>
    <?php endif; ?>
</main>

<?php if ($es_admin): ?>
    
<div class="modal-overlay" id="modal-crear">
    <div class="modal">
        <button class="btn-cerrar" onclick="cerrarModal('modal-crear')">×</button>
        <h2>➕ Agregar título</h2>
        <form method="POST" action="menu.php">
            <input type="hidden" name="accion" value="crear">
            <label>Título *</label>
            <input type="text" name="titulo" placeholder="Ej: Breaking Bad" required>
            <label>Descripción *</label>
            <textarea name="descripcion" placeholder="Breve sinopsis..." required></textarea>
            <label>Género *</label>
            <select name="genero" required>
                <option value="">-- Selecciona --</option>
                <option>Acción</option><option>Aventura</option><option>Ciencia ficción</option>
                <option>Comedia</option><option>Comedia oscura</option><option>Drama</option>
                <option>Drama histórico</option><option>Distopía</option><option>Fantasía</option>
                <option>Misterio</option><option>Terror</option><option>Thriller</option>
                <option>Romance</option><option>Animación</option><option>Documental</option>
            </select>
            <label>Año *</label>
            <input type="number" name="anio" min="1900" max="2099" placeholder="Ej: 2023" required>
            <label>URL de portada <span style="color:#666">(opcional)</span></label>
            <input type="text" name="portada" id="portada-crear" placeholder="https://..."
                   oninput="previsualizarPortada(this, 'prev-crear')">
            <img id="prev-crear" src="" alt="preview">
            <div class="modal-btns">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modal-crear')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-editar">
    <div class="modal">
        <button class="btn-cerrar" onclick="cerrarModal('modal-editar')">×</button>
        <h2>✏️ Editar título</h2>
        <form method="POST" action="menu.php">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" id="editar-id">
            <label>Título *</label>
            <input type="text" name="titulo" id="editar-titulo" required>
            <label>Descripción *</label>
            <textarea name="descripcion" id="editar-descripcion" required></textarea>
            <label>Género *</label>
            <select name="genero" id="editar-genero" required>
                <option value="">-- Selecciona --</option>
                <option>Acción</option><option>Aventura</option><option>Ciencia ficción</option>
                <option>Comedia</option><option>Comedia oscura</option><option>Drama</option>
                <option>Drama histórico</option><option>Distopía</option><option>Fantasía</option>
                <option>Misterio</option><option>Terror</option><option>Thriller</option>
                <option>Romance</option><option>Animación</option><option>Documental</option>
            </select>
            <label>Año *</label>
            <input type="number" name="anio" id="editar-anio" min="1900" max="2099" required>
            <label>URL de portada</label>
            <input type="text" name="portada" id="editar-portada"
                   oninput="previsualizarPortada(this, 'prev-editar')">
            <img id="prev-editar" src="" alt="preview">
            <div class="modal-btns">
                <button type="submit" class="btn-guardar">Actualizar</button>
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modal-editar')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="menu.php" id="form-eliminar" style="display:none">
    <input type="hidden" name="accion" value="eliminar">
    <input type="hidden" name="id" id="eliminar-id">
</form>
<?php endif; ?>

<script>
<?php if ($es_admin): ?>

function abrirModalCrear() {
    document.getElementById('modal-crear').classList.add('activo');
}

function abrirModalEditar(id, titulo, descripcion, genero, anio, portada) {
    document.getElementById('editar-id').value          = id;
    document.getElementById('editar-titulo').value      = titulo;
    document.getElementById('editar-descripcion').value = descripcion;
    document.getElementById('editar-anio').value        = anio;
    document.getElementById('editar-portada').value     = portada;

    const sel = document.getElementById('editar-genero');
    for (let opt of sel.options) {
        if (opt.value === genero) { opt.selected = true; break; }
    }

    const prev = document.getElementById('prev-editar');
    if (portada && portada !== 'img/placeholder.png') {
        prev.src = portada; prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }
    document.getElementById('modal-editar').classList.add('activo');
}

function cerrarModal(id) {
    document.getElementById(id).classList.remove('activo');
}

function confirmarEliminar(id, titulo) {
    if (confirm(`¿Eliminar "${titulo}"?\nEsta acción no se puede deshacer.`)) {
        document.getElementById('eliminar-id').value = id;
        document.getElementById('form-eliminar').submit();
    }
}

function previsualizarPortada(input, previewId) {
    const img = document.getElementById(previewId);
    const url = input.value.trim();
    if (url !== '') {
        img.src = url;
        img.style.display = 'block';
        img.onerror = () => { img.style.display = 'none'; };
        img.onload  = () => { img.style.display = 'block'; };
    } else {
        img.style.display = 'none';
    }
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('activo');
    });
});
<?php endif; ?>
</script>

</body>
</html>
