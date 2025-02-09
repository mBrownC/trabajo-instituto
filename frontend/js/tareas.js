const API_URL = 'http://localhost/proyecto_zapatos3000/public/index.php';

async function crearTarea(titulo, descripcion) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}?accion=crear_tarea`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': token
            },
            body: JSON.stringify({ titulo, descripcion })
        });

        const data = await response.json();

        if (data.mensaje) {
            listarTareas();
            limpiarFormularioTarea();
        } else {
            alert(data.error || 'Error al crear tarea');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocurrió un error al crear la tarea');
    }
}

async function listarTareas() {
    const token = localStorage.getItem('token');
    const listaTareas = document.getElementById('lista-tareas');

    try {
        const response = await fetch(`${API_URL}?accion=listar_tareas`, {
            method: 'GET',
            headers: {
                'Authorization': token
            }
        });

        const data = await response.json();

        if (data.tareas) {
            listaTareas.innerHTML = data.tareas.map(tarea => `
                <div class="tarea">
                    <h3>${tarea.titulo}</h3>
                    <p>${tarea.descripcion}</p>
                    <small>Estado: ${tarea.estado}</small>
                </div>
            `).join('');
        } else {
            listaTareas.innerHTML = '<p>No hay tareas</p>';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Ocurrió un error al listar tareas');
    }
}

function limpiarFormularioTarea() {
    document.getElementById('tarea-titulo').value = '';
    document.getElementById('tarea-descripcion').value = '';
}