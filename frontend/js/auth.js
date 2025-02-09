let enviandoSolicitud = false;

async function login(email, password) {
    if (enviandoSolicitud) return;

    try {
        enviandoSolicitud = true;
        const response = await fetch(`${API_URL}?accion=login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.token) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('usuario', JSON.stringify(data.usuario));
            mostrarDashboard();
        } else {
            alert(data.error || 'Error al iniciar sesión');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocurrió un error al iniciar sesión');
    } finally {
        enviandoSolicitud = false;
    }
}

async function registro(nombre, apellido, email, password) {
    if (enviandoSolicitud) return;

    try {
        enviandoSolicitud = true;
        const response = await fetch(`${API_URL}?accion=registro`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nombre, apellido, email, password })
        });

        const data = await response.json();

        if (data.mensaje) {
            alert(data.mensaje);
            mostrarLogin();
        } else {
            alert(data.error || 'Error al registrarse');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocurrió un error al registrarse');
    } finally {
        enviandoSolicitud = false;
    }
}

async function recuperarContrasena(email) {
    if (enviandoSolicitud) return;

    try {
        enviandoSolicitud = true;
        const response = await fetch(`${API_URL}?accion=solicitar_recuperacion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
        });

        const data = await response.json();

        if (data.mensaje) {
            alert('Instrucciones de recuperación enviadas a tu correo');
        } else {
            alert(data.error || 'Error al solicitar recuperación');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocurrió un error al solicitar recuperación');
    } finally {
        enviandoSolicitud = false;
    }
}

function mostrarDashboard() {
    // Ocultar formulario de login
    document.querySelector('.login-container').style.display = 'none';
    
    // Mostrar dashboard
    document.getElementById('dashboard').style.display = 'block';
}

function cerrarSesion() {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    mostrarLogin();
}

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const registroForm = document.getElementById('registro-form');
    const recuperacionForm = document.getElementById('recuperacion-form');
    const cerrarSesionBtn = document.getElementById('cerrar-sesion');

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            login(email, password);
        });
    }

    if (registroForm) {
        registroForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nombre = document.getElementById('registro-nombre').value;
            const apellido = document.getElementById('registro-apellido').value;
            const email = document.getElementById('registro-email').value;
            const password = document.getElementById('registro-password').value;
            registro(nombre, apellido, email, password);
        });
    }

    if (recuperacionForm) {
        recuperacionForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('recuperacion-email').value;
            recuperarContrasena(email);
        });
    }

    if (cerrarSesionBtn) {
        cerrarSesionBtn.addEventListener('click', cerrarSesion);
    }

    // Verificar si hay token al cargar la página
    const token = localStorage.getItem('token');
    if (token) {
        mostrarDashboard();
    }
});