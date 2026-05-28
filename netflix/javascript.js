document.getElementById('loginForm').addEventListener('submit', function(e) {
    const user = document.getElementById('username').value;
    console.log("Intentando iniciar sesión con el usuario: " + user);
});