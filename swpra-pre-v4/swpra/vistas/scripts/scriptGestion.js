window.addEventListener('popstate', function(e) {

    alert(JSON.stringify(e.state));

    if (e.state === null) {
        menuAdmin.classList.remove('d-none');
        crudProfesores.classList.add('d-none');
        crudEstudiantes.classList.add('d-none');
        return;
    }

    if (e.state.gestion === 'estudiantes') {
        activarCRUDEstudiantes();
        if (typeof e.state.accion !== 'undefined') {
            // Evitar que se pueda regresar a la página
            // donde hubó un queryString 'eliminar':
            this.window.history.replaceState(
                {gestion: e.state.gestion},
                '',
                'Gestion.html?gestion='+e.state.gestion
            );
        }
        return;
    }

    if (e.state.gestion === 'profesores') {
        activarCRUDProfesores();
        if (typeof e.state.accion !== 'undefined') {
            // Evitar que se pueda regresar a la página
            // donde hubó un queryString 'eliminar':
            this.window.history.replaceState(
                {gestion: e.state.gestion},
                '',
                'Gestion.html?gestion='+e.state.gestion
            );
        }
    }

});

window.addEventListener('load', function(e) {

    // Variables globales para el script:
    let formulario = {};
    const notificaciones = this.document.getElementById('notificaciones');


    // Evento para cerrar sesión:
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
            // Muestra la tabla del CRUD:
            activarCRUDEstudiantes();
            solicitarRegistros();
        }
        if (gestion === 'profesores') {
            // Muestra la tabla del CRUD:
            activarCRUDProfesores();
            solicitarRegistros();
        }
    }

    // Agregar los eventos para la funcionaldad de la pagina:
    const refEstudiantes = this.document.getElementById('refEstudiantes');
    const refProfesores  = this.document.getElementById('refProfesores');

    // Evento para activar el CRUD de estudiantes al hacer
    // click en el botón (etiqueta <a>) de la tarjeta estudiantes:
    refEstudiantes.addEventListener('click', function(e) {
        e.preventDefault();
        // Guardar el estado en el historial:
        window.history.pushState({gestion:'estudiantes'}, '', this.search);
        activarCRUDEstudiantes();
        solicitarRegistros();
    });

    // Evento para activar el CRUD de profesores al hacer
    // click en el botón (etiqueta <a>) de la tarjeta estudiantes:
    refProfesores.addEventListener('click', function(e) {
        e.preventDefault();
        // Guardar el estado en el historial:
        window.history.pushState({gestion:'profesores'}, '', this.search);
        activarCRUDProfesores();
    });

    const modalVerEstudiante       = this.document.getElementById('modalVerEstudiante');
    const modalModificarEstudiante = this.document.getElementById('modalModificarEstudiante');

    // Eventos para los formularios del Modal modificar estudiante:
    // Crea un objeto FormData para ser manipulado en la
    // petición asincrona en el modal de confirmación.
    modalModificarEstudiante.querySelectorAll('form')
        .forEach(formularioEnModal => {
            formularioEnModal.addEventListener('submit', function(e) {

                e.preventDefault();

                // Obtener el queryString actual:
                const queryString = new URLSearchParams(window.location.search);
                // Obtener la acción y el email:
                const accion = queryString.get('accion');
                const email =  queryString.get('email');
                // Crear el formulario:
                formulario = new FormData(e.target);
                formulario.append('accion', accion);
                formulario.append('email',  email);
                formulario.append('form',   this.id);

            });
        });
    
    // Evento que muestra el mensaje de confirmación dependiendo de
    // la acción que se desea realizar.
    const modalConfirmarAccion = this.document.getElementById('modalConfirmarAccion');
    modalConfirmarAccion.addEventListener('show.bs.modal', function(e) {
        // Recuperar el botón fuente:
        const btn = e.relatedTarget;
        // Recuperar la acción que se pretende confirmar:
        const loQueHace = btn.getAttribute('data-bs-accion');
        // Recuperar el contenedor 'mensaje' del modal:
        const mensaje = this.querySelector('#mensajeModalConfirmarAccion');
        // Incrustar el mensaje dependiendo de la acción a confirmar:
        if (loQueHace === 'modificar') {
            mensaje.innerHTML = '¿Realmente desea confirmar las modificaciones?';
            // Eliminar propiedades en caso de existir:
            const btnAbortar = this.querySelector('#btnAbortar');
            btnAbortar.removeAttribute('data-bs-dismiss');
            btnAbortar.removeAttribute('data-bs-target');
            btnAbortar.removeAttribute('data-bs-toggle');
            // Vincular el botón 'abortar' para que abra el modal 'modificar':
            btnAbortar.setAttribute('data-bs-target', '#modalModificarEstudiante');
            btnAbortar.setAttribute('data-bs-toggle', 'modal');
            return;
        }
        if (loQueHace === 'eliminar') {
            mensaje.innerHTML = '¿Realmente desea eliminar el registro?';
            // Eliminar propiedades en caso de existir:
            const btnAbortar = this.querySelector('#btnAbortar');
            btnAbortar.removeAttribute('data-bs-dismiss');
            btnAbortar.removeAttribute('data-bs-target');
            btnAbortar.removeAttribute('data-bs-toggle');
            // Vincular el botón 'abortar' para que cierre el modal actual:
            btnAbortar.setAttribute('data-bs-dismiss', 'modal');
        }
        
    });

    const btnConfirmar = modalConfirmarAccion.querySelector('#btnConfirmar');
    btnConfirmar.addEventListener('click', function() {

        const queryStringActual = new URLSearchParams(window.location.search);
        
        const gestion = queryStringActual.get('gestion');
        const accion  = queryStringActual.get('accion');

        if (gestion === 'estudiantes' && accion === 'modificar') {
            modificar();
        }

        if (gestion === 'estudiantes' && accion === 'eliminar') {
            eliminar();
        }

    });


    const eliminar = function() {

        console.log('estoy en función eliminar');
        // Extraer los datos del queryString:
        const queryString = new URLSearchParams(window.location.search);
        const accion = queryString.get('accion');
        const email  = queryString.get('email');
        // Validación:
        if (accion === null || email === null || typeof accion !== 'string' || accion !== 'eliminar') {
            return;
        }
        // Crear el formulario a enviar con la dirección del correo
        // electrónico del registro a eliminar a partir del queryString:
        const formEliminar = new FormData();
        formEliminar.append('accion', accion);
        formEliminar.append('email',  email);
        // Crear el objeto opciones para la petición:
        const opciones = {method:'post', body:formEliminar};
        // Realizar la petición asincrona:
        realizarPeticion('CRUDEstudiantesControlador.php', opciones)
            .then(response => {
    
                if (typeof response === 'undefined') {
                    notificaciones.innerHTML = renderizarNotificacion(
                        'La acción no pudo ser completada.',
                        'alert-warning'
                    );
                    return;
                }
    
                notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');
    
                if (response.datos.registroEliminado) {
                    // Actualizar la tabla CRUD:
                    solicitarRegistros();
                }
    
                return response.datos;
                
            })
            .then(datos => {/* Nada que hacer, la petición no devuelve datos.*/});
            /*const btnConfirmar = modalConfirmarAccion.querySelector('#btnConfirmar');
            btnConfirmar.removeEventListener('click', eliminar);*/
    }

    const modificar = function() {

        console.log('estoy en función modificar');

        const opciones = {method:'post', body:formulario};
        // Realizar la petición asincrona:
        realizarPeticion('CRUDEstudiantesControlador.php', opciones)
            .then(response => {

                if (typeof response === 'undefined') {
                    notificaciones.innerHTML = renderizarNotificacion(
                        'La acción no pudo ser completada.',
                        'alert-warning'
                    );
                    return;
                }

                notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');

                return response.datos;
                
            })
            .then(datos => {});
    }

});

function agregarOpcion(selector, valor, texto, index, actual = false) {
    const opcion = document.createElement('option');
          opcion.value = valor;
          opcion.text  = texto;
    if (actual) {
        selector.options[0].removeAttribute('selected');
        opcion.setAttribute('selected', true);
    }
    selector.options.add(opcion, index+1);
}

// Muestra el CRUD de estudiantes:
function activarCRUDEstudiantes() {

    const menuAdmin       = this.document.getElementById('menuAdmin');
    const crudEstudiantes = this.document.getElementById('crudEstudiantes');
    const crudProfesores  = this.document.getElementById('crudProfesores');

    menuAdmin.classList.add('d-none');
    crudProfesores.classList.add('d-none');
    crudEstudiantes.classList.remove('d-none');

}

// Muestra el CRUD de profesores:
function activarCRUDProfesores() {

    const menuAdmin       = this.document.getElementById('menuAdmin');
    const crudEstudiantes = this.document.getElementById('crudEstudiantes');
    const crudProfesores  = this.document.getElementById('crudProfesores');

    menuAdmin.classList.add('d-none');
    crudEstudiantes.classList.add('d-none');
    crudProfesores.classList.remove('d-none');

}

// Función que solicita los registros que serán mostrados
// en la tabla del CRUD:
function solicitarRegistros() {

    const notificaciones = document.getElementById('notificaciones');

    const modalVerEstudiante       = document.getElementById('modalVerEstudiante');
    const modalModificarEstudiante = document.getElementById('modalModificarEstudiante');

    // Obtener el queryString actual:
    const queryString = new URLSearchParams(window.location.search);
    // Obtener lo que se esta gestionando:
    const tipoGestion = queryString.get('gestion');

    // Definir a que archivo se va a realizar la petición
    // en base al tipo de gestion obtenido:
    let archivo = '';
    let tBodyID = '';

    if (tipoGestion === null) {
        return;
    }
    if (tipoGestion === 'estudiantes') {
        archivo = 'CRUDEstudiantesControlador.php';
        tBodyID = 'tbodyEstudiantes';
    }
    if (tipoGestion === 'profesores') {
        archivo = 'CRUDProfesoresControlador.php';
        tBodyID = 'tbodyProfesores';
    }

    // Crear el formulario con los datos necesarios para realizar
    // la petición:
    const form = new FormData();
    form.append('accion', 'consultarTodo')

    const opciones = {method:'post', body:form};
    
    // Realizar la petición asincrona:
    // Recupera todos los registros disponibles:
    realizarPeticion(archivo, opciones)
        .then(response => {
            if (typeof response === 'undefined') {
                notificaciones.innerHTML = renderizarNotificacion(
                    'Los registros no pudieron ser recuperados.',
                    'text-info'
                );
                return;
            }
            if (response.ok === false) {
                notificaciones.innerHTML = renderizarNotificacion(
                    response.mensaje,
                    'text-info'
                );
                return;
            }
            return response.datos;
        })
        .then(datos => {

            // Esta petición devuelve datos, por lo que hay
            // que hacer la validación:
            if (typeof datos === 'undefined') {
                return;
            }

            // Recuperar la referencia al tbody:
            const tbody = document.getElementById(tBodyID);
            tbody.innerHTML = '';

            // Recuperar el array de usuarios:
            const usuarios = datos.usuarios;

            // Generar una fila por cada elemento del array:
            usuarios.forEach((usuario, index) => {

                // Incorporar las celdas a cada fila nueva:
                const celdas = renderizarCeldas(usuario, index);
                const fila   = tbody.insertRow();
                fila.innerHTML = celdas;

                // Asignar el evento a cada botón para que al
                // hacer click cambie el queryString de la pagina
                fila.querySelectorAll('a').forEach(btnFalso => {
                    btnFalso.addEventListener('click', function(e) {
                        e.preventDefault();
                        actualizarURL(this.search);
                        if (this.name === 'btnVer') {
                            solicitarYCargarDatosEstudianteVer(
                                this.search,
                                modalVerEstudiante
                            );
                        }
                        if (this.name === 'btnModificar') {
                            solicitarYCargarDatosEstudianteModificar(
                                this.search,
                                modalModificarEstudiante
                            );
                        }
                    });
                });
            });
        });
}

// Función que insertar las celdas de cada fila
// de las tablas CRUD:
function renderizarCeldas(usuario, index) {
    return `
        <td>${usuario.email}</td>
        <td>${usuario.nombre}</td>
        <td>${usuario.paterno}</td>
        <td>${usuario.materno === null ? '---' : usuario.materno}</td>
        <td>
            <div class="d-md-flex justify-content-center" style="min-width: 200px;">
                <div class="d-inline-flex px-2">
                    <a href="Gestion?accion=ver&email=${usuario.email}" class="btn btn-primary" name="btnVer" id="btnVer${index}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                            <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                        </svg>
                    </a>
                </div>
                <div class="d-inline-flex px-2">
                    <a href="Gestion?accion=modificar&email=${usuario.email}" class="btn btn-primary" name="btnModificar" id="btnModificar${index}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                        </svg>
                    </a>
                </div>
                <div class="d-inline-flex px-2">
                    <a href="Gestion?accion=eliminar&email=${usuario.email}" class="btn btn-primary" name="btnEliminar" id="btnEliminar${index}" data-bs-target="#modalConfirmarAccion" data-bs-toggle="modal" data-bs-accion="eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </td>
    `;
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

// Función que actualiza el URL cada vez que se hace click
// en alguno de los botones VER, MODIFICAR o ELIMINAR.
function actualizarURL(queryString = '') {

    // Obtener el URL y queryString actual:
    const queryStringActual = new URLSearchParams(window.location.search);
    const url = new URL(window.location);

    // Obtener los parametros del link:
    const aqueryString = new URLSearchParams(queryString);
    const accion = aqueryString.get('accion');
    const email  = aqueryString.get('email');

    // Actualizar el queryStringActual
    queryStringActual.set('accion', accion);
    queryStringActual.set('email',  email);

    url.search = queryStringActual;

    window.history.pushState(
        {
            gestion:queryStringActual.get('gestion'),
            accion: queryStringActual.get('accion'),
            email:  queryStringActual.get('email')
        },
        '',
        url
    );

}

function solicitarYCargarDatosEstudianteVer(txtQueryString = '', modal = HTMLElement) {
    
    const queryString = new URLSearchParams(txtQueryString);
    const accion = queryString.get('accion');
    const email  = queryString.get('email');
    // Crear el formulario:
    const form = new FormData();
    form.append('accion', accion);
    form.append('email',  email);
    // Crear el objeto opciones:
    const opciones = {method:'post', body:form};
    // Realizar la petición asincrona:
    realizarPeticion('CRUDEstudiantesControlador.php', opciones)
        .then(response => {

            // Verificar la respuesta:
            if (typeof response === 'undefined') {
                notificaciones.innerHTML = renderizarNotificacion('Error.', 'alert-warning');
                return;
            }
            if (!response.ok) {
                notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');
            }
            return response.datos;

        }).then(datos => {

            // Verificar la respuesta:
            if (typeof datos === 'undefined') {
                return;
            }

            const usuario = datos.usuario;
            // Cargar los datos en el modal:
            modal.querySelector('#nombreEstudianteVer').innerHTML       = usuario.nombre;
            modal.querySelector('#paternoEstudianteVer').innerHTML      = usuario.paterno;
            modal.querySelector('#maternoEstudianteVer').innerHTML      = usuario.materno === null ? '---' : usuario.materno;
            modal.querySelector('#fnacimientoEstudianteVer').innerHTML  = usuario.fnacimiento;
            modal.querySelector('#sexoEstudianteVer').innerHTML         = usuario.sexo;
            modal.querySelector('#matriculaEstudianteVer').innerHTML    = usuario.matricula === null ? '---' : usuario.matricula;
            modal.querySelector('#programaEstudianteVer').innerHTML     = datos.programaReal === null ? '---' : datos.programaReal.nombre;
            modal.querySelector('#cuatrimestreEstudianteVer').innerHTML = datos.cuatrimestreReal === null ? '---' : datos.cuatrimestreReal.numero;
            modal.querySelector('#emailEstudianteVer').innerHTML        = usuario.email;
            
            const modalObj = new bootstrap.Modal(modal);
            modalObj.show();

        });
}

function solicitarYCargarDatosEstudianteModificar(txtQueryString = '', modal = HTMLElement) {

    const notificaciones = document.getElementById('notificaciones'); 
    const queryString = new URLSearchParams(txtQueryString);
    const accion = queryString.get('accion');
    const email  = queryString.get('email');

    // Crear el formulario:
    const formulario = new FormData();
    formulario.append('accion', 'ver');
    formulario.append('email', email);

    // Crear el objeto opciones:
    const opciones = {method:'post', body:formulario};

    // Realizar la petición asincrona:
    realizarPeticion('CRUDEstudiantesControlador.php', opciones)
        .then(response => {

            // Verificar la respuesta:
            if (typeof response === 'undefined') {
                notificaciones.innerHTML = renderizarNotificacion('Error.', 'alert-warning');
                return;
            }

            if (!response.ok) {
                notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');
            }

            return response.datos;

        }).then(datos => {

            // Verificar la respuesta:
            if (typeof datos === 'undefined') {
                return;
            }

            const usuario = datos.usuario;

            // Cargar los datos en el modal:
            modal.querySelector('#nombreEstudianteModificar').value      = usuario.nombre;
            modal.querySelector('#paternoEstudianteModificar').value     = usuario.paterno;
            modal.querySelector('#maternoEstudianteModificar').value     = usuario.materno;
            modal.querySelector('#fnacimientoEstudianteModificar').value = usuario.fnacimiento;
            modal.querySelectorAll('[name="sexo"]').forEach(radio => {
                if (radio.value.toUpperCase() === usuario.sexo.toUpperCase()) {
                    radio.checked = true;
                    return;
                }
            });

            modal.querySelector('#matriculaEstudianteModificar').value = usuario.matricula;

            const selectorProgramas = modal.querySelector('#programaEstudianteModificar');
            datos.programas.forEach((programa, index) => {
                if (programa.id === usuario.idPrograma) {
                    agregarOpcion(selectorProgramas, programa.id, programa.nombre, index, true);
                } else {
                    agregarOpcion(selectorProgramas, programa.id, programa.nombre, index);
                }
            });

            const selectorCuatrimestres = modal.querySelector('#cuatrimestreEstudianteModificar');
            datos.cuatrimestres.forEach((cuatrimestre, index) => {
                if (cuatrimestre.id === usuario.idCuatrimestre) {
                    agregarOpcion(selectorCuatrimestres, cuatrimestre.id, cuatrimestre.numero, index, true);
                } else {
                    agregarOpcion(selectorCuatrimestres, cuatrimestre.id, cuatrimestre.numero, index);
                }
            });

            modal.querySelector('#emailEstudianteModificar').innerHTML = usuario.email;

            const modalObj = new bootstrap.Modal(modal);
            modalObj.show();

        });
}
