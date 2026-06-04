!!
  NETFLIX — Proyecto Final v1.3 
!!

REQUISITOS:

- XAMPP (o cualquier servidor con Apache + PHP + MySQL)
- PHP 7.4 o superior
- MySQL 5.7 o superior

INSTALACIÓN PASO A PASO:
 
1. Copia la carpeta "netflix/" dentro de:
      C:\xampp\htdocs\   (Windows)
      /opt/lampp/htdocs/ (Linux)

2. Abre el navegador y ve a:
      http://localhost/phpmyadmin

3. Crea la base de datos:
   - Clic en "Nueva" (panel izquierdo)
   - Nombre: proyecto_netflix
   - Clic en "Importar" → sube el archivo "proyecto_netflix.sql"
   - Esto crea las tablas y agrega 10 películas de ejemplo.

4. Abre en el navegador:
      http://localhost/netflix/index.html

5. Regístrate con una cuenta nueva y entra.  

!!
CRUD de Películas
!!

Desde el catálogo (menu.php) el usuario puede:

  ➕ AGREGAR  → Botón "Agregar título" (esquina superior derecha)
                Campos: Título, Descripción, Género, Año, Portada (URL)

  ✏️  EDITAR   → Pasar el mouse sobre cualquier tarjeta → botón verde
                Los campos se pre-llenan con los datos actuales.

  🗑️  ELIMINAR → Pasar el mouse sobre cualquier tarjeta → botón rojo
                Pide confirmación antes de borrar.

  🔍 BUSCAR   → Barra de búsqueda en el header (busca por título)

!!
ESTRUCTURA DE ARCHIVOS
!!

netflix/
├── index.html          ← Pantalla de login
├── registro.html       ← Pantalla de registro
├── menu.php            ← Catálogo principal + CRUD
├── login_process.php   ← Lógica de autenticación
├── register_process.php← Lógica de registro
├── logout.php          ← Cierra sesión
├── conexion.php        ← Conexión a MySQL
├── javascript.js       ← Validación del login
├── register_script.js  ← Validación del registro
├── style.css           ← Estilos de login/registro
├── menu_style.css      ← Estilos extra del catálogo
├── proyecto_netflix.sql← Base de datos completa
├── fondo.jpg           ← Imagen de fondo del login
└── img/
    └── placeholder.png ← Imagen cuando no hay portada
