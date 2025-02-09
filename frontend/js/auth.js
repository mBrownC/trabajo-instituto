const API_URL = 'http://localhost/proyecto_zapatos3000/public/index.php';

async function login(email, password) {
    try {
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
    }
}

async function registro(nombre, apellido, email, password) {
    try {
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
    }
}

async function recuperarContrasena(email) {
    try {
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
    }
}

function cerrarSesion() {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    mostrarLogin();
}