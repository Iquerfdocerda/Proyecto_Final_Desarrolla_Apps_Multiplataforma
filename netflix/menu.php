<?php
session_start();
include('conexion.php');

// 1. Verificación de seguridad: Si no hay sesión, mandarlo al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

// 2. Consulta para traer las 40 películas
$sql = "SELECT * FROM peliculas";
$resultado = mysqli_query($conexion, $sql);

// 3. Obtener el término de búsqueda si el usuario usa la barra
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
if ($busqueda != '') {
    $sql = "SELECT * FROM peliculas WHERE titulo LIKE '%$busqueda%'";
    $resultado = mysqli_query($conexion, $sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Netflix - Catálogo</title>
    <link rel="stylesheet" href="style.css"> <style>
        /* Estilo rápido para la cuadrícula */
       .contenedor-peliculas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    padding: 40px;
    max-width: 1400px;
    margin: auto; /* centra todo */
}

.pelicula-card {
    background: #141414;
    border-radius: 6px;
    overflow: hidden;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.pelicula-card:hover {
    transform: scale(1.08);
    z-index: 2;
}

.pelicula-card img {
    width: 100%;
    aspect-ratio: 2 / 3; /* 🔥 clave para estilo Netflix */
    object-fit: cover;
    display: block;
}

.info {
    padding: 8px;
}

.info h4 {
    font-size: 0.95rem;
    margin: 5px 0;
}

.info p {
    font-size: 0.75rem;
    color: #b3b3b3;
}
/* HEADER GENERAL */
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: black;
    padding: 15px 40px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

/* LOGO */
.logo {
    color: #E50914;
    font-size: 24px;
    font-weight: bold;
}

/* BUSCADOR CENTRADO */
.buscador {
    flex: 1;
    display: flex;
    justify-content: center;
}

.buscador form {
    display: flex;
    background: #141414;
    border-radius: 4px;
    overflow: hidden;
}

.buscador input {
    padding: 8px 12px;
    border: none;
    outline: none;
    background: transparent;
    color: white;
    width: 250px;
}

.buscador button {
    background: #E50914;
    border: none;
    color: white;
    padding: 8px 12px;
    cursor: pointer;
}

/* USUARIO DERECHA */
.usuario {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 14px;
}

.usuario a {
    color: white;
    text-decoration: none;
}

.usuario a:hover {
    text-decoration: underline;
}
    </style>
</head>
<body style="background-color: #141414; color: white; font-family: Arial, sans-serif;">

    <header class="header">
    <div class="logo">NETFLIX</div>

    <div class="buscador">
        <form action="menu.php" method="GET">
            <input type="text" name="buscar" placeholder="Buscar..." value="<?php echo $busqueda; ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <div class="usuario">
        <span>Bienvenido, <?php echo $_SESSION['nombre_real']; ?></span>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>

    <main class="contenedor-peliculas">
        <?php
        if (mysqli_num_rows($resultado) > 0) {
            while($row = mysqli_fetch_assoc($resultado)) {
                ?>
                <div class="pelicula-card">
                    <img src="<?php echo $row['portada']; ?>" alt="<?php echo $row['titulo']; ?>">
                    
                    <div class="info">
                        <h4><?php echo $row['titulo']; ?></h4>
                        <p style="font-size: 0.8em; color: #b3b3b3;"><?php echo $row['genero']; ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No se encontraron películas.</p>";
        }
        ?>
    </main>

</body>
</html>