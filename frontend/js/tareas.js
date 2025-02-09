async function crearTarea(titulo, descripcion) {
    const token = localStorage.getItem("token");

    try {
        const response = await fetch(`${API_URL}?accion=crear_tarea`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: token,
            },
            body: JSON.stringify({ titulo, descripcion }),
        });

        const data = await response.json();

        if (data.mensaje) {
            listarTareas();
            limpiarFormularioTarea();
        } else {
            alert(data.error || "Error al crear tarea");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Ocurrió un error al crear la tarea");
    }
}

async function editarTarea(id, titulo, descripcion, estado) {
    const token = localStorage.getItem("token");

    try {
        const response = await fetch(`${API_URL}?accion=actualizar_tarea`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: token,
            },
            body: JSON.stringify({ id, titulo, descripcion, estado }),
        });

        const data = await response.json();

        if (data.mensaje) {
            listarTareas();
            // Opcional: Cerrar modal de edición
            document.getElementById("editar-tarea-modal").style.display = "none";
        } else {
            alert(data.error || "Error al actualizar tarea");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Ocurrió un error al actualizar la tarea");
    }
}

async function eliminarTarea(id) {
    const token = localStorage.getItem("token");

    try {
        const response = await fetch(`${API_URL}?accion=eliminar_tarea&id=${id}`, {
            method: "DELETE",
            headers: {
                Authorization: token,
            },
        });

        const data = await response.json();

        if (data.mensaje) {
            listarTareas();
        } else {
            alert(data.error || "Error al eliminar tarea");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Ocurrió un error al eliminar la tarea");
    }
}

// Modificar función listarTareas para incluir botones de edición y eliminación
async function listarTareas() {
    const token = localStorage.getItem("token");
    const listaTareas = document.getElementById("lista-tareas");

    try {
        const response = await fetch(`${API_URL}?accion=listar_tareas`, {
            method: "GET",
            headers: {
                Authorization: token,
            },
        });

        const data = await response.json();

        if (data.tareas) {
            listaTareas.innerHTML = data.tareas
                .map(
                    (tarea) => `
                <div class="tarea" id="tarea-${tarea.id}">
                    <h3>${tarea.titulo}</h3>
                    <p>${tarea.descripcion}</p>
                    <small>Estado: ${tarea.estado}</small>
                    <div class="acciones-tarea">
                        <button onclick="mostrarModalEdicion(${tarea.id}, '${tarea.titulo}', '${tarea.descripcion}', '${tarea.estado}')">Editar</button>
                        <button onclick="confirmarEliminarTarea(${tarea.id})">Eliminar</button>
                    </div>
                </div>
            `
                )
                .join("");
        } else {
            listaTareas.innerHTML = "<p>No hay tareas</p>";
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Ocurrió un error al listar tareas");
    }
}

// Funciones para manejar modal de edición
function mostrarModalEdicion(id, titulo, descripcion, estado) {
    const modal = document.getElementById("editar-tarea-modal");
    document.getElementById("editar-tarea-id").value = id;
    document.getElementById("editar-tarea-titulo").value = titulo;
    document.getElementById("editar-tarea-descripcion").value = descripcion;
    document.getElementById("editar-tarea-estado").value = estado;
    modal.style.display = "block";
}

function confirmarEliminarTarea(id) {
    if (confirm("¿Estás seguro de eliminar esta tarea?")) {
        eliminarTarea(id);
    }
}

// Event listener para formulario de edición
document.addEventListener("DOMContentLoaded", () => {
    const editarTareaForm = document.getElementById("editar-tarea-form");
    if (editarTareaForm) {
        editarTareaForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const id = document.getElementById("editar-tarea-id").value;
            const titulo = document.getElementById("editar-tarea-titulo").value;
            const descripcion = document.getElementById(
                "editar-tarea-descripcion"
            ).value;
            const estado = document.getElementById("editar-tarea-estado").value;

            editarTarea(id, titulo, descripcion, estado);
        });
    }
});

function limpiarFormularioTarea() {
    document.getElementById("tarea-titulo").value = "";
    document.getElementById("tarea-descripcion").value = "";
}
