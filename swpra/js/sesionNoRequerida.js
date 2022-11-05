// Función que realiza peticiones asincronas al servidor.
async function realizarPeticion(archivo, opciones) {

    const url = 'http://localhost/swpra/controladores/' + archivo;
    try {
        const respuesta = await fetch(url, opciones);
        if (respuesta.ok) {
            return respuesta.json();
        }
        throw new Error("No hubo respuesta de la API.");
    } catch (error) {
        alert(error);
    }

}

// Función que renderiza notificaciones.
function renderizarNotificacion(mensaje, clase) {
    return `
        <div class="alert ${clase} alert-dismissible fade show" role="alert">
            <strong>${mensaje}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}

// Evento que verifica la sesión antes de cargar la página por completo.
document.addEventListener('readystatechange', function () {
    if (this.readyState === 'interactive') {
        realizarPeticion('SesionControlador.php', {}).then(json => {
            if (json.ok) {
                if (json.datos.sesion === 'activa') {
                    window.location.replace('http://localhost/swpra/vistas/Perfil.html');
                }
            }
        });
    }
});