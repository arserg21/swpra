window.addEventListener('popstate', function(e) {

    if (e.state === null) {
        menuAdmin.classList.remove('d-none');
        crudProfesores.classList.add('d-none');
        crudEstudiantes.classList.add('d-none');
        return;
    }

    if (e.state.gestion === 'estudiantes') {
        activarCRUDEstudiantes();
        return;
    }

    if (e.state.gestion === 'profesores') {
        activarCRUDProfesores();
    }

});

window.addEventListener('load', function(e) {

    document.getElementById('refSalir') // Elemento: <a></a>
        .addEventListener('click', function(e) {
            e.preventDefault();
            //cerrarSesion(this.search);
        });

    // Verificar el queryString

    const parametrosURL = new URLSearchParams(this.window.location.search);
    const gestion = parametrosURL.get('gestion');

    if (gestion !== null) {
        if (gestion === 'estudiantes') {
            activarCRUDEstudiantes();
        }
        if (gestion === 'profesores') {
            activarCRUDProfesores();
        }
    }

    const refEstudiantes = this.document.getElementById('refEstudiantes');
    const refProfesores  = this.document.getElementById('refProfesores');

    refEstudiantes.addEventListener('click', function(e) {
        e.preventDefault();
        window.history.pushState({gestion:'estudiantes'}, '', this.search);
        activarCRUDEstudiantes();
    });

    refProfesores.addEventListener('click', function(e) {
        e.preventDefault();
        window.history.pushState({gestion:'profesores'}, '', this.search);
        activarCRUDProfesores();
    });

});

function activarCRUDEstudiantes() {

    const menuAdmin       = this.document.getElementById('menuAdmin');
    const crudEstudiantes = this.document.getElementById('crudEstudiantes');
    const crudProfesores  = this.document.getElementById('crudProfesores');

    menuAdmin.classList.add('d-none');
    crudProfesores.classList.add('d-none');
    crudEstudiantes.classList.remove('d-none');

}

function activarCRUDProfesores() {

    const menuAdmin       = this.document.getElementById('menuAdmin');
    const crudEstudiantes = this.document.getElementById('crudEstudiantes');
    const crudProfesores  = this.document.getElementById('crudProfesores');

    menuAdmin.classList.add('d-none');
    crudEstudiantes.classList.add('d-none');
    crudProfesores.classList.remove('d-none');

}

/*function cerrarSesion(queryString) {

    const parametros = new URLSearchParams(queryString);
    const accion     = parametros.get('accion');

    if (accion !== null) {

        const contenidorAnimacion = document.getElementById('salida');
        const tarjetaPerfil       = document.getElementById('tajetaPerfil');

        const form = new FormData();
        form.append('accion', accion);
        const opciones = {method:'post', body:form};
        
        tarjetaPerfil.classList.add('d-none');
        renderizarSalida(contenidorAnimacion);

        realizarPeticion('SesionControlador.php', opciones).then(json => {

            if (typeof json === 'undefined') {
                contenidorAnimacion.innerHTML = '';
                contenidorAnimacion.classList.add('d-none');
                tarjetaPerfil.classList.remove('d-none');
                return;
            }

            if (json.ok) {

                setTimeout((() => location.reload()), 3000);

            } else {
                contenidorAnimacion.innerHTML = '';
                contenidorAnimacion.classList.add('d-none');
                tarjetaPerfil.classList.remove('d-none');
            }

        });
    }
}*/