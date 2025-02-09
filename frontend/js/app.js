document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');

    // Elementos de autenticación
    const loginForm = document.getElementById('login-form');
    const registroForm = document.getElementById('registro-form');
    const recuperacionForm = document.getElementById('recuperacion-form');

    // Elementos de navegación
    const registroLink = document.getElementById('registro-link');
    const loginLinks = document.querySelectorAll('#login-link, #login-link-recuperacion');
    const recuperarLink = document.getElementById('recuperar-link');

    // Elementos del dashboard
    const crearTareaBtn = document.getElementById('crear-tarea');
    const cerrarSesionBtn = document.getElementById('cerrar-sesion');

    // Navegación entre formularios
    registroLink.addEventListener('click', mostrarRegistro);
    loginLinks.forEach(link => link.addEventListener('click', mostrarLogin));
    recuperarLink.addEventListener('click', mostrarRecuperacion);

    // Formularios
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        login(email, password);
    });

    registroForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const nombre = document.getElementById('registro-nombre').value;
        const apellido = document.getElementById('registro-apellido').value;
        const email = document.getElementById('registro-email').value;
        const password = document.getElementById('registro-password').value;
        registro(nombre, apellido, email, password);
    });

    recuperacionForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('recuperacion-email').value;
        recuperarContrasena(email);
    });

    // Dashboard
    crearTareaBtn.addEventListener('click', () => {
        const titulo = document.getElementById('tarea-titulo').value;
        const descripcion = document.getElementById('tarea-descripcion').value;
        crearTarea(titulo, descripcion);
    });

    cerrarSesionBtn.addEventListener('click', cerrarSesion);

    // Verificar sesión
    if (token) {
        mostrarDashboard();
    } else {
        mostrarLogin();
    }
});

function mostrarLogin() {
    document.querySelector('.login-container').style.display = 'block';
    document.querySelector('.registro-container').style.display = 'none';
    document.querySelector('.recuperacion-container').style.display = 'none';
    document.getElementById('auth-section').style.display = 'block';
    document.getElementById('dashboard').style.display = 'none';
}

function mostrarRegistro() {
    document.querySelector('.login-container').style.display = 'none';
    document.querySelector('.registro-container').style.display = 'block';
    document.querySelector('.recuperacion-container').style.display = 'none';
}

function mostrarRecuperacion() {
    document.querySelector('.login-container').style.display = 'none';
    document.querySelector('.registro-container').style.display = 'none';
    document.querySelector('.recuperacion-container').style.display = 'block';
}

function mostrarDashboard() {
    document.getElementById('auth-section').style.display = 'none';
    document.getElementById('dashboard').style.display = 'block';
    listarTareas();
}