<?php
// menu.php: Catálogo principal + CRUD de películas
session_start();
include('conexion.php');

// Si no hay una sesion activa se redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

$mensaje = ''; // Variable para mostrar mensajes de éxito o error al usuario

//CREATE: Insertar nueva película o serie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {

    $titulo      = trim($_POST['titulo']);       // trim() quita espacios al inicio y al final
    $descripcion = trim($_POST['descripcion']);
    $genero      = trim($_POST['genero']);
    $anio        = intval($_POST['anio']);       // intval() convierte a número entero y evita datos raros
    $portada     = trim($_POST['portada']);

    // Si no se puso una URL de imagen de portada, se pone automaticamente el placeholder de la carpeta img/
    if ($portada === '') {
        $portada = 'img/placeholder.png';
    }

    $stmt = $conexion->prepare(
        "INSERT INTO peliculas (titulo, descripcion, genero, anio, portada)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssis", $titulo, $descripcion, $genero, $anio, $portada);

    if ($stmt->execute()) {
        $mensaje = "✅ «{$titulo}» agregada al catálogo.";
    } else {
        $mensaje = "❌ Error al agregar la película.";
    }
}

// ── UPDATE: Actualizar película existente ────────────────────
// Se ejecuta cuando el usuario guarda los cambios del formulario de edición.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $id          = intval($_POST['id']);
    $titulo      = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $genero      = trim($_POST['genero']);
    $anio        = intval($_POST['anio']);
    $portada     = trim($_POST['portada']);

    if ($portada === '') {
        $portada = 'img/placeholder.png';
    }

    $stmt = $conexion->prepare(
        "UPDATE peliculas SET titulo=?, descripcion=?, genero=?, anio=?, portada=?
         WHERE id=?"
    );
    
    $stmt->bind_param("sssisi", $titulo, $descripcion, $genero, $anio, $portada, $id);

    if ($stmt->execute()) {
        $mensaje = "✅ «{$titulo}» actualizada correctamente.";
    } else {
        $mensaje = "❌ Error al actualizar.";
    }
}

// ── DELETE: Eliminar película ────────────────────────────────
// Se activa cuando el usuario confirma eliminar una tarjeta.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {

    $id = intval($_POST['id']);

    $stmt = $conexion->prepare("DELETE FROM peliculas WHERE id = ?");
    $stmt->bind_param("i", $id);   // "i" = integer

    if ($stmt->execute()) {
        $mensaje = "🗑️ Película eliminada.";
    } else {
        $mensaje = "❌ Error al eliminar.";
    }
}

// Para buscar peliculas en la BD
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '') {
    // Para buscar se usa LIKE '%texto%' para buscar coincidencias parciales.
    $stmt = $conexion->prepare("SELECT * FROM peliculas WHERE titulo LIKE ? ORDER BY id DESC");
    $termino = "%{$busqueda}%";
    $stmt->bind_param("s", $termino);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    // Si no hay busquedas aparecen todas y las mas recientes primero
    $resultado = mysqli_query($conexion, "SELECT * FROM peliculas ORDER BY id DESC");
}

// Si se quiere editar cargamos los datos de esa película
$pelicula_editar = null;
if (isset($_GET['editar_id'])) {
    $editar_id = intval($_GET['editar_id']);
    $stmt_e = $conexion->prepare("SELECT * FROM peliculas WHERE id = ?");
    $stmt_e->bind_param("i", $editar_id);
    $stmt_e->execute();
    $pelicula_editar = $stmt_e->get_result()->fetch_assoc();
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

        body {
            background-color: #141414;
            color: white;
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }

        /* ── HEADER ────────────────────────────────────────── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #000;
            padding: 14px 40px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.8);
        }

        .logo { color: #E50914; font-size: 26px; font-weight: bold; letter-spacing: 2px; }

        .buscador { flex: 1; display: flex; justify-content: center; }

        .buscador form { display: flex; background: #222; border-radius: 4px; overflow: hidden; border: 1px solid #444; }

        .buscador input {
            padding: 8px 14px; border: none; outline: none;
            background: transparent; color: white; width: 260px; font-size: 14px;
        }

        .buscador button {
            background: #E50914; border: none; color: white;
            padding: 8px 16px; cursor: pointer; font-size: 14px;
        }

        .buscador button:hover { background: #b9090b; }

        .usuario { display: flex; align-items: center; gap: 16px; font-size: 14px; }

        .usuario span { color: #ccc; }

        .usuario a { color: #aaa; text-decoration: none; }
        .usuario a:hover { color: white; }

        /* BOTÓN AGREGAR */
        .btn-agregar {
            background: #E50914; color: white; border: none;
            padding: 10px 20px; border-radius: 4px; cursor: pointer;
            font-size: 14px; font-weight: bold;
        }
        .btn-agregar:hover { background: #b9090b; }

        /* MENSAJE DE ÉXITO/ERROR */
        .mensaje {
            text-align: center;
            padding: 12px 20px;
            margin: 16px 40px 0;
            border-radius: 4px;
            font-size: 15px;
            background: #1a3a1a;
            border-left: 4px solid #46d369;
        }
        .mensaje.error { background: #3a1a1a; border-left-color: #E50914; }
        
        .modal-overlay {
            display: none; 
            position: fixed;
            inset: 0;   
            background: rgba(0,0,0,0.85);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.activo { display: flex; } 

        .modal {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 36px;
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .modal h2 { color: #E50914; margin-bottom: 24px; font-size: 22px; }

        .modal label { display: block; color: #aaa; font-size: 13px; margin-bottom: 5px; margin-top: 14px; }

        .modal input,
        .modal textarea,
        .modal select {
            width: 100%;
            padding: 10px 12px;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 4px;
            color: white;
            font-size: 14px;
        }

        .modal textarea { resize: vertical; min-height: 80px; }

        .modal input:focus,
        .modal textarea:focus,
        .modal select:focus { outline: none; border-color: #E50914; }

        .modal-btns { display: flex; gap: 12px; margin-top: 24px; }

        .btn-guardar {
            flex: 1; padding: 12px; background: #E50914; color: white;
            border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 15px;
        }
        .btn-guardar:hover { background: #b9090b; }

        .btn-cancelar {
            flex: 1; padding: 12px; background: #333; color: white;
            border: none; border-radius: 4px; cursor: pointer; font-size: 15px;
        }
        .btn-cancelar:hover { background: #444; }

        .btn-cerrar {
            position: absolute; top: 12px; right: 16px;
            background: none; border: none; color: #888;
            font-size: 24px; cursor: pointer; line-height: 1;
        }
        .btn-cerrar:hover { color: white; }

        /* SECCIÓN DE ENCABEZADO DEL CATÁLOGO */
        .catalogo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 28px 40px 10px;
        }

        .catalogo-header h2 { font-size: 20px; color: #ccc; }

        /* GRID DE TARJETAS */
        .contenedor-peliculas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 18px;
            padding: 20px 40px 60px;
        }

        .pelicula-card {
            background: #1c1c1c;
            border-radius: 6px;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
            position: relative;
        }

        .pelicula-card:hover {
            transform: scale(1.06);
            z-index: 10;
            box-shadow: 0 8px 25px rgba(0,0,0,0.7);
        }

        .pelicula-card img {
            width: 100%;
            aspect-ratio: 2 / 3; 
            object-fit: cover;
            display: block;
            background: #2a2a2a;
        }

        .info {
            padding: 10px;
        }

        .info h4 { font-size: 0.9rem; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .info .genero { font-size: 0.75rem; color: #46d369; font-weight: bold; }

        .info .anio   { font-size: 0.75rem; color: #888; }

        /* BOTONES CRUD SOBRE LA TARJETA */
        .crud-btns {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .pelicula-card:hover .crud-btns { opacity: 1; }

        .btn-editar,
        .btn-eliminar {
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-editar   { background: rgba(70,211,105,0.9); color: #000; }
        .btn-editar:hover { background: #46d369; }

        .btn-eliminar { background: rgba(229,9,20,0.9); color: white; }
        .btn-eliminar:hover { background: #E50914; }

        /* SIN RESULTADOS */
        .sin-resultados {
            grid-column: 1 / -1; /* Ocupa todo el ancho del grid */
            text-align: center;
            padding: 60px 20px;
            color: #555;
            font-size: 18px;
        }

        /* PREVIEW DE PORTADA EN EL MODAL */
        #preview-portada {
            margin-top: 10px;
            width: 80px;
            height: 110px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #444;
            display: none;
        }
    </style>
</head>
<body>

<! HEADER >
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
        <span>Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre_real']); ?></strong></span>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>

<! MENSAJE DE OPERACIÓN >
<?php if ($mensaje): ?>
    <div class="mensaje <?php echo str_starts_with($mensaje, '❌') ? 'error' : ''; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<! ENCABEZADO DEL CATÁLOGO >
<div class="catalogo-header">
    <h2>
        <?php if ($busqueda): ?>
            Resultados para: "<?php echo htmlspecialchars($busqueda); ?>"
            &nbsp;<a href="menu.php" style="font-size:13px; color:#888;">Ver todo</a>
        <?php else: ?>
            Catálogo completo
        <?php endif; ?>
    </h2>

    <!-- Botón que abre el modal de AGREGAR -->
    <button class="btn-agregar" onclick="abrirModalCrear()">+ Agregar título</button>
</div>

<! GRID DE PELÍCULAS >
<main class="contenedor-peliculas">
    <?php
    if (mysqli_num_rows($resultado) > 0):
        while ($row = mysqli_fetch_assoc($resultado)):
    ?>
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

            <! BOTONES CRUD >
            <div class="crud-btns">
                <!-- Editar: pasa los datos de esta película al modal de edición -->
                <button class="btn-editar" onclick="abrirModalEditar(
                    <?php echo $row['id']; ?>,
                    <?php echo json_encode($row['titulo']); ?>,
                    <?php echo json_encode($row['descripcion']); ?>,
                    <?php echo json_encode($row['genero']); ?>,
                    <?php echo $row['anio']; ?>,
                    <?php echo json_encode($row['portada']); ?>
                )">✏️ Editar</button>

                <! Para eliminar se pide confirmación antes de enviar el formulario >
                <button class="btn-eliminar" onclick="confirmarEliminar(
                    <?php echo $row['id']; ?>,
                    <?php echo json_encode($row['titulo']); ?>
                )">🗑️ Eliminar</button>
            </div>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <div class="sin-resultados">
            <p>No se encontraron títulos<?php echo $busqueda ? " para \"$busqueda\"" : ''; ?>.</p>
        </div>
    <?php endif; ?>
</main>

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
                <option>Acción</option>
                <option>Aventura</option>
                <option>Ciencia ficción</option>
                <option>Comedia</option>
                <option>Comedia oscura</option>
                <option>Drama</option>
                <option>Drama histórico</option>
                <option>Distopía</option>
                <option>Fantasía</option>
                <option>Misterio</option>
                <option>Terror</option>
                <option>Thriller</option>
                <option>Romance</option>
                <option>Animación</option>
                <option>Documental</option>
            </select>

            <label>Año *</label>
            <input type="number" name="anio" min="1900" max="2099"
                   placeholder="Ej: 2023" required>

            <label>URL de portada <span style="color:#666">(opcional)</span></label>
            <input type="text" name="portada" id="portada-crear"
                   placeholder="https://..." oninput="previsualizarPortada(this, 'prev-crear')">
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
            <!-- Este campo oculto guarda el id de la película a editar -->
            <input type="hidden" name="id"     id="editar-id">

            <label>Título *</label>
            <input type="text" name="titulo" id="editar-titulo" required>

            <label>Descripción *</label>
            <textarea name="descripcion" id="editar-descripcion" required></textarea>

            <label>Género *</label>
            <select name="genero" id="editar-genero" required>
                <option value="">-- Selecciona --</option>
                <option>Acción</option>
                <option>Aventura</option>
                <option>Ciencia ficción</option>
                <option>Comedia</option>
                <option>Comedia oscura</option>
                <option>Drama</option>
                <option>Drama histórico</option>
                <option>Distopía</option>
                <option>Fantasía</option>
                <option>Misterio</option>
                <option>Terror</option>
                <option>Thriller</option>
                <option>Romance</option>
                <option>Animación</option>
                <option>Documental</option>
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
    <input type="hidden" name="id"     id="eliminar-id">
</form>


<! JAVASCRIPT DEL MENÚ >
<script>

function abrirModalCrear() {
    // Obtenemos el elemento por su id y le añadimos la clase 'activo'.
    // El CSS tiene: .modal-overlay.activo { display: flex; }
    document.getElementById('modal-crear').classList.add('activo');
}

function abrirModalEditar(id, titulo, descripcion, genero, anio, portada) {
    // Ponemos los valores en cada campo del formulario de edición
    document.getElementById('editar-id').value          = id;
    document.getElementById('editar-titulo').value      = titulo;
    document.getElementById('editar-descripcion').value = descripcion;
    document.getElementById('editar-anio').value        = anio;
    document.getElementById('editar-portada').value     = portada;

    const selectGenero = document.getElementById('editar-genero');
    for (let option of selectGenero.options) {
        if (option.value === genero) {
            option.selected = true;
            break;
        }
    }

    const prevImg = document.getElementById('prev-editar');
    if (portada && portada !== 'img/placeholder.png') {
        prevImg.src   = portada;
        prevImg.style.display = 'block';
    } else {
        prevImg.style.display = 'none';
    }

    document.getElementById('modal-editar').classList.add('activo');
}

function cerrarModal(idModal) {
    document.getElementById(idModal).classList.remove('activo');
}

function confirmarEliminar(id, titulo) {
    if (confirm(`¿Estás seguro de eliminar "${titulo}"?\nEsta acción no se puede deshacer.`)) {
        document.getElementById('eliminar-id').value = id;
        document.getElementById('form-eliminar').submit();
    }
}

function previsualizarPortada(input, previewId) {
    const img = document.getElementById(previewId);
    const url = input.value.trim();

    if (url !== '') {
        img.src   = url;
        img.style.display = 'block';
        img.onerror = () => { img.style.display = 'none'; };
        img.onload  = () => { img.style.display = 'block'; };
    } else {
        img.style.display = 'none';
    }
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('activo');
        }
    });
});
</script>

</body>
</html>
