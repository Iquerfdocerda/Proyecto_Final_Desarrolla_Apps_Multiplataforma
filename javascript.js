// ============================================================
// javascript.js — Validación del formulario de Login
// BUG CORREGIDO: index.html llamaba a "script.js" (que no existía).
//                Ahora llama correctamente a "javascript.js".
// ============================================================

// Esperamos a que todo el HTML esté cargado antes de ejecutar el JS.
// Esto es importante: si el script corre antes de que exista el formulario,
// getElementById devuelve null y el addEventListener falla.
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('loginForm');

    // addEventListener escucha el evento 'submit' del formulario.
    // Se dispara cuando el usuario hace clic en "Iniciar Sesión".
    form.addEventListener('submit', function(e) {
        const usuario = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        // Validación básica del lado del cliente (antes de enviar al servidor)
        if (usuario === '' || password === '') {
            e.preventDefault(); // Cancela el envío del formulario
            alert('Por favor completa todos los campos.');
            return;
        }

        // Solo para debug: muestra en consola quién intenta entrar
        // (puedes borrarlo en producción)
        console.log("Intentando iniciar sesión con el usuario: " + usuario);
    });
});
