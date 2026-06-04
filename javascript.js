
// Esperamos a que todo el HTML esté cargado antes de ejecutar el JS porque si no falla
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('loginForm');

    // addEventListener escucha el evento "submit" del formulario.
    // Se activa cuando el usuario hace clic en "Iniciar Sesión".
    form.addEventListener('submit', function(e) {
        const usuario = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        // Validación básica del lado del cliente antes de enviar al server
        if (usuario === '' || password === '') {
            e.preventDefault(); // Se cancela el envío del formulario
            alert('Por favor completa todos los campos.');
            return;
        }

        // Solo para debug: muestra en consola quién intenta entrar
        console.log("Intentando iniciar sesión con el usuario: " + usuario);
    });
});
