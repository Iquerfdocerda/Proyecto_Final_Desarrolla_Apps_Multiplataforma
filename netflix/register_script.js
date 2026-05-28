document.getElementById('registerForm').addEventListener('submit', function(e) {
    const pass = document.getElementById('pass').value;
    const confPass = document.getElementById('conf_pass').value;
    if (pass !== confPass) {
        e.preventDefault(); 
        alert("¡Las contraseñas no coinciden! Revisa de nuevo.");
    }
});