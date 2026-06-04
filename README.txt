============================================================
  NETFLIX CLONE — Proyecto Final v1.3 (CORREGIDA)
============================================================

REQUISITOS
----------
- XAMPP (o cualquier servidor con Apache + PHP + MySQL)
- PHP 7.4 o superior
- MySQL 5.7 o superior

INSTALACIÓN PASO A PASO
------------------------
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

============================================================
BUGS CORREGIDOS (v1.2 → v1.3)
============================================================

BUG 1 — style.css: bloque .secondary-text sin cerrar
  CAUSA:  Faltaba el } de cierre en .secondary-text, lo que
          hacía que .login-container y los estilos de input
          no funcionaran correctamente.
  FIX:    Agregado el } faltante.

BUG 2 — index.html: llamaba a "script.js" (no existía)
  CAUSA:  <script src="script.js"> pero el archivo real es
          "javascript.js". El JS del login nunca corría.
  FIX:    Cambiado a <script src="javascript.js">.

BUG 3 — proyecto_netflix.sql: tabla Cuentas incompleta
  CAUSA:  La tabla solo tenía (Usuario_id, Usuario, Contrasena)
          pero el PHP buscaba (id, nombre, apellido, email,
          edad, password). Incompatibilidad total.
  FIX:    Tabla rediseñada con todos los campos necesarios.

BUG 4 — proyecto_netflix.sql: tabla peliculas no existía
  CAUSA:  menu.php hacía SELECT * FROM peliculas pero esa
          tabla nunca fue creada en el SQL.
  FIX:    Tabla creada con campos (id, titulo, descripcion,
          genero, anio, portada) + 10 registros de ejemplo.

BUG 5 — SQL Injection en login_process.php y register_process.php
  CAUSA:  Las queries se construían concatenando variables de
          usuario directamente en el string SQL.
          Ej: "SELECT * FROM Cuentas WHERE usuario='$var'"
          Un atacante podía escribir ' OR '1'='1 para entrar
          sin contraseña.
  FIX:    Reemplazado por Prepared Statements con bind_param().

BUG 6 — menu_style.css nunca se cargaba
  CAUSA:  El archivo existía pero ningún HTML/PHP lo importaba.
  FIX:    Agregado al <head> de menu.php como animaciones.

============================================================
NUEVA FUNCIONALIDAD: CRUD de Películas
============================================================
Desde el catálogo (menu.php) el usuario puede:

  ➕ AGREGAR  → Botón "Agregar título" (esquina superior derecha)
                Campos: Título, Descripción, Género, Año, Portada (URL)

  ✏️  EDITAR   → Pasar el mouse sobre cualquier tarjeta → botón verde
                Los campos se pre-llenan con los datos actuales.

  🗑️  ELIMINAR → Pasar el mouse sobre cualquier tarjeta → botón rojo
                Pide confirmación antes de borrar.

  🔍 BUSCAR   → Barra de búsqueda en el header (busca por título)

============================================================
ESTRUCTURA DE ARCHIVOS
============================================================
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

============================================================
