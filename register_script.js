
// register_script.js: Validación del formulario de registro

document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('registerForm');

    form.addEventListener('submit', function(e) {
        const pass     = document.getElementById('pass').value;
        const confPass = document.getElementById('conf_pass').value;

        // Verificamos que las dos contraseñas sean iguales antes de enviar el formulario 
        // e.preventDefault() cancela el envío si no coinciden.
        if (pass !== confPass) {
            e.preventDefault();
            alert('¡Las contraseñas no coinciden! Revísalas.');
            return;
        }

        // se valida longitud mínima de contraseña
        if (pass.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres.');
            return;
        }
    });
});
