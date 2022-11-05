window.addEventListener('popstate', function(e) {

    if (e.state.gestion === null) {
        return;
    }

    if (e.state.gestion === 'estudiantes') {
        activarCRUDEstudiantes();
        if (typeof e.state.accion !== 'undefined' && e.state.accion !== 'buscar') {
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
        if (typeof e.state.accion !== 'undefined' && e.state.accion !== 'buscar') {
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

    // Verificar el queryString
    const parametrosURL = new URLSearchParams(this.window.location.search);
    const gestion = parametrosURL.get('gestion');
    const accion =  parametrosURL.get('accion');

    if (gestion !== null) {
        if (gestion === 'estudiantes' && (accion === null || accion !== 'buscar')) {
            
            // Muestra la tabla del CRUD:
            window.history.replaceState({gestion:'estudiantes'}, '', 'Gestion.html?gestion=estudiantes');
            activarCRUDEstudiantes();
            solicitarRegistros();
        }
        if (gestion === 'profesores' && (accion !== null || accion !== 'buscar')) {
            // Muestra la tabla del CRUD:
            window.history.replaceState({gestion:'profesores'}, '', 'Gestion.html?gestion=profesores');
            activarCRUDProfesores();
            solicitarRegistros();
        }
        if ((gestion === 'profesores' || gestion === 'estudiantes') && accion !== null && accion === 'buscar') {
            if (gestion === 'profesores') {
                activarCRUDProfesores();
            }
            if (gestion === 'estudiantes') {
                activarCRUDEstudiantes();
            }
            solicitarBusqueda();
        }
    }
    
    const refEstudiantes           = this.document.getElementById('refEstudiantes');
    const refProfesores            = this.document.getElementById('refProfesores');
    const modalVerEstudiante       = this.document.getElementById('modalVerEstudiante');
    const modalModificarEstudiante = this.document.getElementById('modalModificarEstudiante');
    const modalConfirmarAccion     = this.document.getElementById('modalConfirmarAccion');
    const formBuscarEstudiante     = document.getElementById('formBuscarEstudiante');
    const modalCrearEstudiante     = this.document.getElementById('modalCrearEstudiante');
    const formCrearEstudiante      = document.getElementById('formCrearEstudiante');

    const btnConfirmar = modalConfirmarAccion.querySelector('#btnConfirmar');
    const btnLimpiarBusquedaEstudiante = document.getElementById('btnLimpiarBusquedaEstudiante');

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
        solicitarRegistros();
    });

    btnLimpiarBusquedaEstudiante.addEventListener('click', function() {
        window.history.replaceState(
            {gestion:'estudiantes'},
            '',
            'Gestion.html?gestion=estudiantes'
        );
        activarCRUDEstudiantes();
        solicitarRegistros();
    });

    // Eventos para los formularios del Modal modificar estudiante:
    // Crea un objeto FormData para ser manipulado en la
    // petición asincrona en el modal de confirmación.
    modalModificarEstudiante.querySelectorAll('form').forEach(formularioEnModal => {
        
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
            
            new bootstrap.Modal(modalConfirmarAccion).toggle();
            bootstrap.Modal.getInstance(modalModificarEstudiante).toggle();
        });
    });

    // Evento que muestra el mensaje de confirmación dependiendo de
    // la acción que se desea realizar.
    modalConfirmarAccion.addEventListener('show.bs.modal', function(e) {
        // Recuperar el botón fuente:
        //const btn = e.relatedTarget;
        // Recuperar la acción que se pretende confirmar:
        const queryStringActual = new URLSearchParams(window.location.search);
        const loQueHace = queryStringActual.get('accion');

        // Recuperar el contenedor 'mensaje' del modal:
        const mensaje = this.querySelector('#mensajeModalConfirmarAccion');
        const btnAbortar = this.querySelector('#btnAbortar');
        // Incrustar el mensaje dependiendo de la acción a confirmar:
        if (loQueHace === 'modificar') {
            mensaje.innerHTML = '¿Realmente desea confirmar las modificaciones?';
            // Eliminar propiedades en caso de existir:
            btnAbortar.removeAttribute('data-bs-dismiss');
            btnAbortar.removeAttribute('data-bs-target');
            btnAbortar.removeAttribute('data-bs-toggle');
            // Vincular el botón 'abortar' para que abra el modal 'modificar':
            btnAbortar.setAttribute('data-bs-target', '#modalModificarEstudiante');
            btnAbortar.setAttribute('data-bs-toggle', 'modal');
        }
        if (loQueHace === 'eliminar') {
            mensaje.innerHTML = '¿Realmente desea eliminar el registro?';
            // Eliminar propiedades en caso de existir:
            btnAbortar.removeAttribute('data-bs-dismiss');
            btnAbortar.removeAttribute('data-bs-target');
            btnAbortar.removeAttribute('data-bs-toggle');
            // Vincular el botón 'abortar' para que cierre el modal actual:
            btnAbortar.setAttribute('data-bs-dismiss', 'modal');
        }
    });

    btnConfirmar.addEventListener('click', function() {

        const queryStringActual = new URLSearchParams(window.location.search);
        
        const gestion = queryStringActual.get('gestion');
        const accion  = queryStringActual.get('accion');

        if (gestion === 'estudiantes' && accion === 'modificar') {
            modificarEstudiante();
        }

        if (gestion === 'estudiantes' && accion === 'eliminar') {
            eliminarEstudiante();
        }

    });

    modalModificarEstudiante.addEventListener('hidden.bs.modal', function() {
        bootstrap.Modal.getInstance(modalModificarEstudiante).dispose();
        if (bootstrap.Modal.getInstance(modalConfirmarAccion) === null) {
            window.history.replaceState({gestion:'estudiantes'}, '', 'Gestion.html?gestion=estudiantes');
            limpiarModalModificarEstudiante();
        }
    });

    modalConfirmarAccion.addEventListener('hidden.bs.modal', function() {
        bootstrap.Modal.getInstance(modalConfirmarAccion).dispose();
        if (bootstrap.Modal.getInstance(modalModificarEstudiante) === null) {
            window.history.replaceState({gestion:'estudiantes'}, '', 'Gestion.html?gestion=estudiantes');
            limpiarModalModificarEstudiante();
        }
    });

    modalVerEstudiante.addEventListener('hidden.bs.modal', function() {
        limpiarModalVerEstudiante();
    });

    modalCrearEstudiante.addEventListener('hidden.bs.modal', function() {
        limpiarModalCrearEstudiante();
        bootstrap.Modal.getInstance(modalCrearEstudiante).dispose();
    });

    formBuscarEstudiante.addEventListener('submit', function(e) {

        e.preventDefault();
        const preform = new FormData(e.target);

        const queryStringNuevo = new URLSearchParams(window.location.search);
        queryStringNuevo.set('accion', 'buscar');
        queryStringNuevo.set('email',  preform.get('emailEstudiante'));

        actualizarURL(queryStringNuevo.toString());

        solicitarBusqueda();
        
    });

    formCrearEstudiante.addEventListener('submit', function(e) {

        e.preventDefault();

        // Recuperar el formulario:
        const form = new FormData(e.target);
        form.append('accion', 'crear');

        const opciones = {method:'post', body:form};

        realizarPeticion('CRUDEstudiantesControlador.php', opciones)
            .then(response => {
                
                if (typeof response === 'undefined') {
                    notificaciones.innerHTML = renderizarNotificacion(
                        'Error',
                        'alert-danger'
                    );
                    return;
                }

                notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');
                solicitarRegistros();

            });
        
    });

    btnNuevoEstudiante.addEventListener('click', function() {

        const form = new FormData();
        form.append('accion', 'obtenerExtras');

        const opciones = {method:'post', body:form};

        realizarPeticion('CRUDEstudiantesControlador.php', opciones)
            .then(response => {

                if (typeof response === 'undefined') {
                    notificaciones.innerHTML = renderizarNotificacion('Error', 'alert-danger');
                    return;
                }

                if (!response.ok) {
                    notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');
                    return;
                }

                return response.datos;

            })
            .then(datos => {

                if (typeof datos === 'undefined') {
                    return;
                }

                const cuatrimestres = datos.cuatrimestres;
                const programas = datos.programas;

                const selectorProgramas = formCrearEstudiante.querySelector('#programaEstudianteCrear');
                programas.forEach((programa, index) => {
                    agregarOpcion(selectorProgramas, programa.id, programa.nombre, index);
                });

                const selectorCuatrimestres = formCrearEstudiante.querySelector('#cuatrimestreEstudianteCrear');
                cuatrimestres.forEach((cuatrimestre, index) => {
                    agregarOpcion(selectorCuatrimestres, cuatrimestre.id, cuatrimestre.numero, index);
                });

                new bootstrap.Modal(modalCrearEstudiante).toggle();

            });

    });

    cargarEventosInputsFormCrear();

    const eliminarEstudiante = function() {

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
                        'Error.',
                        'alert-danger'
                    );
                    return;
                }
    
                notificaciones.innerHTML = renderizarNotificacion(response.mensaje, 'alert-info');
    
                if (response.ok && response.datos.registroEliminado) {
                    // Actualizar la tabla CRUD:
                    solicitarRegistros();
                }
    
                return response.datos;
                
            });
    }

    const modificarEstudiante = function() {

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

                if (response.ok) {
                    // Actualizar la tabla CRUD:
                    solicitarRegistros();
                }

                return response.datos;
                
            })
            .then(datos => {});
    }

});

// Función que solicita los registros que serán mostrados
// en la tabla del CRUD dependiendo del tipo de gestión actual:
function solicitarRegistros() {

    const notificaciones = document.getElementById('notificaciones');

    const modalVerEstudiante       = document.getElementById('modalVerEstudiante');
    const modalModificarEstudiante = document.getElementById('modalModificarEstudiante');
    const modalConfirmarAccion     = document.getElementById('modalConfirmarAccion');

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
                    'Error.',
                    'alert-danger'
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

            if (usuarios === null || usuarios.length === 0) {

                if (tipoGestion === 'estudiantes') {
                    document.getElementById('tablaCRUDEstudiantes').classList.add('d-none');
                    document.getElementById('bannerEstudiantes').classList.remove('d-none');
                }

                if (tipoGestion === 'profesores') {
                    document.getElementById('tablaCRUDProfesores').classList.add('d-none');
                    document.getElementById('bannerProfesores').classList.remove('d-none');
                }
                
                return;
            } else {

                if (tipoGestion === 'estudiantes') {
                    document.getElementById('tablaCRUDEstudiantes').classList.remove('d-none');
                    document.getElementById('bannerEstudiantes').classList.add('d-none');
                }

                if (tipoGestion === 'profesores') {
                    document.getElementById('tablaCRUDProfesores').classList.remove('d-none');
                    document.getElementById('bannerProfesores').classList.add('d-none');
                }

            }

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
                            solicitarDatosEstudiante(
                                this.search,
                                modalVerEstudiante
                            );
                            return;
                        }

                        if (this.name === 'btnModificar') {
                            solicitarDatosEstudiante(
                                this.search,
                                modalModificarEstudiante
                            );
                            return;
                        }

                        if (this.name === 'btnEliminar') {
                            new bootstrap.Modal(modalConfirmarAccion)
                                .show();
                        }

                    });
                });
            });
        });
}

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

function solicitarDatosEstudiante(txtQueryString = '', modal = HTMLElement) {

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

            // Cargar los datos en el modal:
            if (accion === 'ver') {
                cargarDatosModalVerEstudiante(datos);
            }
            if (accion === 'modificar') {
                cargarDatosModalModificarEstudiante(datos);
            }

            const modalObj = new bootstrap.Modal(modal);
            modalObj.show();

        });
}

// Función que carga los datos del estudiante recibidos por parámetro
// en el Modal 'ver estudiante':
function cargarDatosModalVerEstudiante(datos = {}) {

    const usuario = datos.usuario;
    const programaReal = datos.programaReal;
    const cuatrimestreReal = datos.cuatrimestreReal;

    const modal = document.getElementById('modalVerEstudiante');

    modal.querySelector('#nombreEstudianteVer').innerHTML       = usuario.nombre;
    modal.querySelector('#paternoEstudianteVer').innerHTML      = usuario.paterno;
    modal.querySelector('#maternoEstudianteVer').innerHTML      = usuario.materno === null ? '---' : usuario.materno;
    modal.querySelector('#fnacimientoEstudianteVer').innerHTML  = usuario.fnacimiento;
    modal.querySelector('#sexoEstudianteVer').innerHTML         = usuario.sexo;
    modal.querySelector('#matriculaEstudianteVer').innerHTML    = usuario.matricula === null ? '---' : usuario.matricula;
    modal.querySelector('#programaEstudianteVer').innerHTML     = datos.programaReal === null ? '---' : programaReal.nombre;
    modal.querySelector('#cuatrimestreEstudianteVer').innerHTML = datos.cuatrimestreReal === null ? '---' : cuatrimestreReal.numero;
    modal.querySelector('#emailEstudianteVer').innerHTML        = usuario.email;

}

// Función que carga los datos del estudiante recibidos por parámetro
// en el Modal 'modificar estudiante':
function cargarDatosModalModificarEstudiante(datos = {}) {

    const usuario = datos.usuario;

    const modal = document.getElementById('modalModificarEstudiante');

    const btnSubmit1 = modal.querySelector('#btnGuardarModificarEstudiante1');
    const btnSubmit2 = modal.querySelector('#btnGuardarModificarEstudiante2');
    const btnSubmit3 = modal.querySelector('#btnGuardarModificarEstudiante3');

    const notificaciones = modal.querySelector('[name="notificacionesModal"]');

    const nombre = modal.querySelector('#nombreEstudianteModificar');
    nombre.value = usuario.nombre;
    nombre.addEventListener('input', () => validarNombre(nombre, notificaciones, btnSubmit1));

    const paterno = modal.querySelector('#paternoEstudianteModificar');
    paterno.value = usuario.paterno;
    paterno.addEventListener('input', () => validarPaterno(paterno, notificaciones, btnSubmit1));

    const materno = modal.querySelector('#maternoEstudianteModificar');
    materno.value = usuario.materno;
    materno.addEventListener('input', () => validarMaterno(materno, notificaciones, btnSubmit1));

    const fnacimiento = modal.querySelector('#fnacimientoEstudianteModificar');
    fnacimiento.value = usuario.fnacimiento;

    modal.querySelectorAll('[name="sexo"]').forEach(radio => {
        if (radio.value.toUpperCase() === usuario.sexo.toUpperCase()) {
            radio.checked = true;
            return;
        }
    });

    const matricula = modal.querySelector('#matriculaEstudianteModificar');
    matricula.value = usuario.matricula;
    matricula.addEventListener('input', () => validarMatricula(matricula, notificaciones, btnSubmit2));
    
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

    const contrasena1 = modal.querySelector('#contrasenaNuevaEstudiante1');
    const contrasena2 = modal.querySelector('#contrasenaNuevaEstudiante2');
    
    contrasena1.addEventListener('input', function() {
        validarContrasena(this, notificaciones, btnSubmit3);
        if (contrasena2.value.length !== 0) {
            validarContrasenas(this, contrasena2, notificaciones, btnSubmit3);
        }
    });

    contrasena2.addEventListener('input', function() {
        validarContrasena(this, notificaciones, btnSubmit3);
        if (contrasena1.value.length !== 0) {
            validarContrasenas(this, contrasena1, notificaciones, btnSubmit3);
        }
    });

}

// Función que agrega opciones a un selector:
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

// Función que insertar las celdas de cada fila de las tablas CRUD:
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
                    <a href="Gestion?accion=eliminar&email=${usuario.email}" class="btn btn-primary" name="btnEliminar" id="btnEliminar${index}" data-bs-accion="eliminar">
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

// Función para limpiar los datos del Modal 'ver estudiante':
function limpiarModalVerEstudiante() {

    const modal = document.getElementById('modalVerEstudiante');

    modal.querySelector('#nombreEstudianteVer').innerHTML       = '';
    modal.querySelector('#paternoEstudianteVer').innerHTML      = '';
    modal.querySelector('#maternoEstudianteVer').innerHTML      = '';
    modal.querySelector('#fnacimientoEstudianteVer').innerHTML  = '';
    modal.querySelector('#sexoEstudianteVer').innerHTML         = '';
    modal.querySelector('#matriculaEstudianteVer').innerHTML    = '';
    modal.querySelector('#programaEstudianteVer').innerHTML     = '';
    modal.querySelector('#cuatrimestreEstudianteVer').innerHTML = '';
    modal.querySelector('#emailEstudianteVer').innerHTML        = '';

}

// Función para limpiar los datos del Modal 'modificar estudiante':
function limpiarModalModificarEstudiante() {

    const modal = document.getElementById('modalModificarEstudiante');

    console.log('voy a remover: ' + modal.id);

    modal.querySelector('#nombreEstudianteModificar').value      = '';
    modal.querySelector('#paternoEstudianteModificar').value     = '';
    modal.querySelector('#maternoEstudianteModificar').value     = '';
    modal.querySelector('#fnacimientoEstudianteModificar').value = '';
    modal.querySelectorAll('[name="sexo"]').forEach(radio => {
        radio.removeAttribute('checked');
    });
    modal.querySelector('#matriculaEstudianteModificar').value = '';
    
    const selectorProgramas = modal.querySelector('#programaEstudianteModificar');
    while(selectorProgramas.length > 1) {
        selectorProgramas.remove(selectorProgramas.length - 1);
        console.log('removi un programa');
    }

    const selectorCuatrimestres = modal.querySelector('#cuatrimestreEstudianteModificar');
    while(selectorCuatrimestres.options.length > 1) {
        selectorCuatrimestres.remove(selectorCuatrimestres.length - 1);
        console.log('removi un cuatri');
    }

    modal.querySelector('#emailEstudianteModificar').innerHTML = '';
    modal.querySelector('#contrasenaNuevaEstudiante1').value = '';
    modal.querySelector('#contrasenaNuevaEstudiante2').value = '';

    modal.querySelector('[name="notificacionesModal"]').innerHTML = '';
}

// Función para limpiar los datos del Modal 'crear estudiante':
function limpiarModalCrearEstudiante() {

    const modal = document.getElementById('modalCrearEstudiante');

    console.log('voy a remover: ' + modal.id);

    modal.querySelector('#nombreEstudianteCrear').value      = '';
    modal.querySelector('#paternoEstudianteCrear').value     = '';
    modal.querySelector('#maternoEstudianteCrear').value     = '';
    modal.querySelector('#fnacimientoEstudianteCrear').value = '';
    modal.querySelectorAll('[name="sexo"]').forEach(radio => {
        radio.removeAttribute('checked');
    });
    modal.querySelector('#matriculaEstudianteCrear').value = '';
    
    const selectorProgramas = modal.querySelector('#programaEstudianteCrear');
    while(selectorProgramas.length > 1) {
        selectorProgramas.remove(selectorProgramas.length - 1);
        console.log('removi un programa');
    }

    const selectorCuatrimestres = modal.querySelector('#cuatrimestreEstudianteCrear');
    while(selectorCuatrimestres.options.length > 1) {
        selectorCuatrimestres.remove(selectorCuatrimestres.length - 1);
        console.log('removi un cuatri');
    }

    modal.querySelector('#emailEstudianteCrear').value = '';
    modal.querySelector('#contrasenaEstudianteCrear1').value = '';
    modal.querySelector('#contrasenaEstudianteCrear2').value = '';

    modal.querySelector('[name="notificacionesModal"]').innerHTML = '';

}

// Función que solicita la busqueda de un estudiante en base a una dirección de correo electrónico:
function solicitarBusqueda() {

    // Obtener el queryString actual
    const queryStringActual = new URLSearchParams(window.location.search);
    const gestion = queryStringActual.get('gestion');
    const accion  = queryStringActual.get('accion'); // =buscar
    const email   = queryStringActual.get('email');

    // Definir a que archivo se va a realizar la petición
    // en base al tipo de gestion obtenido:
    let archivo = '';
    let tBodyID = '';

    if (gestion === null) {
        return;
    }
    if (gestion === 'estudiantes') {
        archivo = 'CRUDEstudiantesControlador.php';
        tBodyID = 'tbodyEstudiantes';
    }
    if (gestion === 'profesores') {
        archivo = 'CRUDProfesoresControlador.php';
        tBodyID = 'tbodyProfesores';
    }

    // Crear un formulario con los datos
    const form = new FormData();
    form.append('accion', accion);
    form.append('email',  email);

    if (email === '' || email === null) {
        return;
    }

    // Crear el objeto opciones para la petición:
    const opciones = {method:'post', body:form};

    // Solicitar la petición asincrona:
    realizarPeticion(archivo, opciones)
        .then(response => {

            if (typeof response === 'undefined') {
                notificaciones.innerHTML = renderizarNotificacion(
                    'Error',
                    'alert-danger'
                );
                return;
            }

            if (!response.ok) {
                notificaciones.innerHTML = renderizarNotificacion(
                    response.mensaje,
                    'alert-info'
                );
                return;
            }

            return response.datos;

        })
        .then(datos => {

            if (typeof datos === 'undefined') {
                return;
            }

            const usuario = datos.usuario;

            // Recuperar la referencia al tbody:
            const tbody = document.getElementById(tBodyID);
            tbody.innerHTML = '';

            if (usuario === null) {

                if (gestion === 'estudiantes') {
                    document.getElementById('tablaCRUDEstudiantes').classList.add('d-none');
                    document.getElementById('bannerEstudiantes').classList.remove('d-none');
                }

                if (gestion === 'profesores') {
                    document.getElementById('tablaCRUDProfesores').classList.add('d-none');
                    document.getElementById('bannerProfesores').classList.remove('d-none');
                }
                
                return;
            } else {

                if (gestion === 'estudiantes') {
                    document.getElementById('tablaCRUDEstudiantes').classList.remove('d-none');
                    document.getElementById('bannerEstudiantes').classList.add('d-none');
                }

                if (gestion === 'profesores') {
                    document.getElementById('tablaCRUDProfesores').classList.remove('d-none');
                    document.getElementById('bannerProfesores').classList.add('d-none');
                }

            }

            // Incorporar las celdas a cada fila nueva:
            const celdas = renderizarCeldas(usuario, 0);
            const fila   = tbody.insertRow();
            fila.innerHTML = celdas;

            // Asignar el evento a cada botón para que al
            // hacer click cambie el queryString de la pagina
            fila.querySelectorAll('a').forEach(btnFalso => {
                btnFalso.addEventListener('click', function(e) {

                    e.preventDefault();

                    actualizarURL(this.search);

                    if (this.name === 'btnVer') {
                        solicitarDatosEstudiante(
                            this.search,
                            modalVerEstudiante
                        );
                        return;
                    }

                    if (this.name === 'btnModificar') {
                        solicitarDatosEstudiante(
                            this.search,
                            modalModificarEstudiante
                        );
                        return;
                    }

                    if (this.name === 'btnEliminar') {
                        new bootstrap.Modal(modalConfirmarAccion)
                            .show();
                    }

                });
            });


        });

}

// Función para cargar los eventos de los componentes del formulario de registro de estudiantes:
function cargarEventosInputsFormCrear() {

    const formulario = document.getElementById('formCrearEstudiante');
    const notificaciones = formulario.querySelector('[name="notificacionesModal"]');
    const btnSubmit = formulario.querySelector('[name="btnFinalizarRegistro"]');

    const nombre = formulario.querySelector('#nombreEstudianteCrear');
    const paterno = formulario.querySelector('#paternoEstudianteCrear');
    const materno = formulario.querySelector('#maternoEstudianteCrear');
    const matricula = formulario.querySelector('#matriculaEstudianteCrear');
    const email = formulario.querySelector('#emailEstudianteCrear');
    const contrasena1 = formulario.querySelector('#contrasenaEstudianteCrear1');
    const contrasena2 = formulario.querySelector('#contrasenaEstudianteCrear2');

    nombre.addEventListener('input', () => validarNombre(nombre, notificaciones, btnSubmit));
    paterno.addEventListener('input', () => validarPaterno(paterno, notificaciones, btnSubmit));
    materno.addEventListener('input', () => validarMaterno(materno, notificaciones, btnSubmit));

    matricula.addEventListener('input', () => validarMatricula(matricula, notificaciones, btnSubmit));
    contrasena1.addEventListener('input', function() {
        validarContrasena(this, notificaciones, btnSubmit);
        if (contrasena2.value.length !== 0) {
            validarContrasenas(this, contrasena2, notificaciones, btnSubmit);
        }
    });

    contrasena2.addEventListener('input', function() {
        validarContrasena(this, notificaciones, btnSubmit);
        if (contrasena1.value.length !== 0) {
            validarContrasenas(this, contrasena1, notificaciones, btnSubmit);
        }
    });

    email.addEventListener('input', () => validarEmail(email, notificaciones, btnSubmit));

}