!!
  NETFLIX — Proyecto Final Netflix
!!

REQUISITOS:

- XAMPP
- PHP 7.4 o superior
- MySQL 5.7 o superior

INSTALACIÓN PASO A PASO:
 
1. Copia la carpeta "netflix/" dentro de:
      C:\xampp\htdocs\   (Windows)
      /opt/lampp/htdocs/ (Linux)

2. Abre el navegador y ve a:
      http://localhost/phpmyadmin

3. Crea la base de datos:
   - Clic en "Nueva"
   - Nombre: proyecto_netflix
   - Clic en "Importar" y sube el archivo: "proyecto_netflix.sql"

4. Abre en el navegador:
      http://localhost/netflix/index.html

5. Regístrate con una cuenta nueva y entra.  

!!
CRUD de Películas
!!

Desde el catálogo (menú.php) el usuario puede:

  ➕ AGREGAR: poniendo título. descripción, una URL de imagen o si se queda en blanco se usa una imagen provisional.

  ✏️  EDITAR: para poder editar los datos anteriores de cualquier película/serie que esté ahí.

  🗑️  ELIMINAR: para eliminar cualquier película/serie y pide una confirmación antes de borrar.

  🔍 BUSCAR: busca por título y si no se busca nada pone las agregadas recientemente primero.

!!
ESTRUCTURA DE ARCHIVOS
!!

netflix/
├── index.html: Pantalla de login
├── registro.html: Pantalla de registro
├── menu.php: Catálogo principal y el CRUD
├── login_process.php: Lógica de autenticación
├── register_process.php: Lógica de registro
├── logout.php: Cierra sesión
├── conexion.php: Conexión a MySQL
├── javascript.js: Validación del login
├── register_script.js: Validación del registro
├── style.css: Estilos de login/registro
├── menu_style.css: Estilos extra del catálogo
├── proyecto_netflix.sql: Base de datos
├── fondo.jpg: Imagen de fondo del login
└── img/
    └── placeholder.png: Imagen cuando no hay portada
