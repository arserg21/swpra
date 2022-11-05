
window.addEventListener('load', function() {

    //const notificaciones   = this.document.getElementById('notificaciones');

    const readonlyIDsForm1 = ['nombre', 'paterno', 'materno', 'fnacimiento'];
    const disabledIDsForm1 = ['sexo1', 'sexo2'];
    const navegacion1      = document.getElementById('navegacion1');
    //const btnCancelar1     = document.getElementById('btnCancelar1');

    const readonlyIDsForm2 = ['matricula'];
    const disabledIDsForm2 = ['programa', 'cuatrimestre'];

    const readonlyIDsForm22 = ['cedula'];
    const disabledIDsForm22 = ['programa2'];

    const navegacion2      = document.getElementById('navegacion2');
    const navegacion22     = document.getElementById('navegacion22');
    //const btnCancelar2     = document.getElementById('btnCancelar2');

    //let copiaDatosPersonales = {'nombre':'', 'paterno':'', 'materno':'', 'fnacimiento':'', 'sexo':''};
    //let copiaDatosEscolares  = {'matricula':'', 'programa':0, 'cuatrimestre':0};

    solicitarDatos();

    document.getElementById('refSalir') // Elemento: <a></a>
        .addEventListener('click', function(e) {
            e.preventDefault();
            cerrarSesion(this.search);
        });
    
    /*document.getElementById('formFoto')
        .addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('a');
            cambiarFoto();
        });*/

    document.getElementById('cambiarContrasena') // Elemento: <a></a>
        .addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('form1').classList.add('d-none');
            document.getElementById('form2').classList.add('d-none');
            document.getElementById('datosAcceso').classList.add('d-none');
            document.getElementById('form3').classList.remove('d-none');
        });
    
    document.getElementById('btnCancelar3')
        .addEventListener('click', function(e) {
            document.getElementById('form1').classList.remove('d-none');
            document.getElementById('form2').classList.remove('d-none');
            document.getElementById('datosAcceso').classList.remove('d-none');
            document.getElementById('form3').classList.add('d-none');
            // Devolver los datos de los componentes a su estado original:
            document.getElementById('form3').querySelectorAll('input').forEach(input => input.value = '');
        });

    document.getElementById('btnMod1') // Elemento: <button></button>
        .addEventListener('click', function() {
            // Crear una copia de los datos:
            activarElementos(true, readonlyIDsForm1, disabledIDsForm1);
            navegacion1.classList.remove('d-none');
        });
    
    document.getElementById('btnMod2') // Elemento: <button></button>
        .addEventListener('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const rol = urlParams.get('rol');
            
            if (rol === null) {
                return;
            }

            if (rol === 'estudiante') {
                activarElementos(true, readonlyIDsForm2, disabledIDsForm2);
                navegacion2.classList.remove('d-none');
            }

            if (rol === 'profesor') {
                activarElementos(true, readonlyIDsForm22, disabledIDsForm22);
                navegacion22.classList.remove('d-none');
            }
            
        });

    /*document.getElementById('btnMod22') // Elemento: <button></button>
        .addEventListener('click', function() {
            activarElementos(true, readonlyIDsForm22, disabledIDsForm22);
            navegacion22.classList.remove('d-none');
        });*/
    
    document.getElementById('btnCancelar1')
        .addEventListener('click', function() {
            navegacion1.classList.add('d-none');
            activarElementos(false, readonlyIDsForm1, disabledIDsForm1);
            // Devolver los datos de los componentes a su estado original:
        });
    
    document.getElementById('btnCancelar2')
        .addEventListener('click', function() {
            navegacion2.classList.add('d-none');
            activarElementos(false, readonlyIDsForm2, disabledIDsForm2);
            // Devolver los datos de los componentes a su estado original:
        });
    
    document.getElementById('btnCancelar22')
        .addEventListener('click', function() {
            navegacion22.classList.add('d-none');
            activarElementos(false, readonlyIDsForm22, disabledIDsForm22);
            // Devolver los datos de los componentes a su estado original:
        });

    document.querySelectorAll('form')
        .forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                solicitarActualizacion(e);
            });
        });

});

// Cargar los datos del usuario estudiante en los formularios:
function cargarEstudianteEnInterfaz(usuario = {}, extras = {}) {
    // Carga del form 1:
    document.getElementById('nombreCentral').innerHTML = usuario.nombre;
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('paterno').value = usuario.paterno;
    document.getElementById('materno').value = usuario.materno;
    document.getElementById('fnacimiento').value = usuario.fnacimiento;
    document.querySelectorAll('[name="sexo"]').forEach(function(elemento) {
        if (elemento.value.toUpperCase() === usuario.sexo.toUpperCase()) {
            elemento.checked = true;
            return;
        }
    });
    // Carga del form 2:
    const selectorProgramas = document.getElementById('programa');
    const selectorCuatris   = document.getElementById('cuatrimestre');

    document.getElementById('matricula').value = usuario.matricula;

    extras.programas.forEach((programa, index) => {
        if (programa.id === usuario.idPrograma) {
            agregarOpcion(selectorProgramas, programa.id, programa.nombre, index, true);
        } else {
            agregarOpcion(selectorProgramas, programa.id, programa.nombre, index);
        }
    });

    extras.cuatrimestres.forEach((cuatrimestre, index) => {
        if (cuatrimestre.id === usuario.idCuatrimestre) {
            agregarOpcion(selectorCuatris, cuatrimestre.id, cuatrimestre.numero, index, true);
        } else {
            agregarOpcion(selectorCuatris, cuatrimestre.id, cuatrimestre.numero, index);
        }
    });
    // Carga del form 3:
    document.getElementById('email').innerHTML = usuario.email;

}

// Cargar los datos del usuario estudiante en los formularios:
function cargarProfesorEnInterfaz(usuario = {}, extras = {}) {
    
    // Carga del form 1:
    document.getElementById('nombreCentral').innerHTML = usuario.nombre;
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('paterno').value = usuario.paterno;
    document.getElementById('materno').value = usuario.materno;
    document.getElementById('fnacimiento').value = usuario.fnacimiento;
    document.querySelectorAll('[name="sexo"]').forEach(function(elemento) {
        if (elemento.value.toUpperCase() === usuario.sexo.toUpperCase()) {
            elemento.checked = true;
            return;
        }
    });

    const selectorProgramas = document.getElementById('programa2');

    document.getElementById('cedula').value = usuario.cedula;

    extras.programas.forEach((programa, index) => {
        if (programa.id === usuario.idPrograma) {
            agregarOpcion(selectorProgramas, programa.id, programa.nombre, index, true);
        } else {
            agregarOpcion(selectorProgramas, programa.id, programa.nombre, index);
        }
    });

    document.getElementById('email').innerHTML = usuario.email;

}

// Carga los datos del usuario en el formulario 1:
function cargarEnForm1(usuario = {}) {

    document.getElementById('nombreCentral').innerHTML = usuario.nombre;
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('paterno').value = usuario.paterno;
    document.getElementById('materno').value = usuario.materno;
    document.getElementById('fnacimiento').value = usuario.fnacimiento;
    document.querySelectorAll('[name="sexo"]').forEach(function(elemento) {
        if (elemento.value.toUpperCase() === usuario.sexo.toUpperCase()) {
            elemento.checked = true;
            return;
        }
    });

}

// Carga los datos del usuario en el formulario 2:
function cargarEnForm2(usuario = {}, extras = {}) {

    const selectorProgramas = document.getElementById('programa');
    const selectorCuatris   = document.getElementById('cuatrimestre');

    document.getElementById('matricula').value = usuario.matricula;

    extras.programas.forEach((programa, index) => {
        if (programa.id === usuario.idPrograma) {
            agregarOpcion(selectorProgramas, programa.id, programa.nombre, index, true);
        } else {
            agregarOpcion(selectorProgramas, programa.id, programa.nombre, index);
        }
    });

    extras.cuatrimestres.forEach((cuatrimestre, index) => {
        if (cuatrimestre.id === usuario.idCuatrimestre) {
            agregarOpcion(selectorCuatris, cuatrimestre.id, cuatrimestre.numero, index, true);
        } else {
            agregarOpcion(selectorCuatris, cuatrimestre.id, cuatrimestre.numero, index);
        }
    });

}

// Carga los datos del usuario en el formulario 3:
function cargarEnForm3(usuario = {}) {
    document.getElementById('email').innerHTML = usuario.email;
}

/*function cargarEnInterfaz(usuario = {}, formato = {}) {

    document.getElementById('nombreCentral').innerHTML = usuario.nombre;
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('paterno').value = usuario.paterno;
    document.getElementById('materno').value = usuario.materno;
    document.getElementById('fnacimiento').value = usuario.fnacimiento;
    document.getElementById('matricula').value = usuario.matricula;
    document.getElementById('email').innerHTML = usuario.email;

    document.querySelectorAll('[name="sexo"]').forEach(function(elemento) {
        if (elemento.value.toUpperCase() === usuario.sexo.toUpperCase()) {
            elemento.checked = true;
            return;
        }
    });

    formato.programas.forEach((programa, index, array, objetivo = document.getElementById('programa')) => {
        if (programa.id === usuario.idPrograma) {
            agregarOpcion(objetivo, programa.id, programa.nombre, index, true);
        } else {
            agregarOpcion(objetivo, programa.id, programa.nombre, index);
        }
    });

    formato.cuatrimestres.forEach((cuatrimestre, index, array, objetivo = document.getElementById('cuatrimestre')) => {
        if (cuatrimestre.id === usuario.idCuatrimestre) {
            agregarOpcion(objetivo, cuatrimestre.id, cuatrimestre.numero, index, true);
        } else {
            agregarOpcion(objetivo, cuatrimestre.id, cuatrimestre.numero, index);
        }
    });

}*/

function agregarOpcion(selector, valor, texto, index, actual = false, ) {
    //console.log(index);
    const opcion = document.createElement('option');
          opcion.value = valor;
          opcion.text  = texto;
    if (actual) {
        selector.options[0].removeAttribute('selected');
        opcion.setAttribute('selected', true);
    }
    selector.options.add(opcion, index+1);
}

/* Función que activa los componentes de un formulario:
 * Parámetros: ID's de los elementos que tienen el atributo 'readonly'.
 *             ID's de los elementos que tienen el atributo 'disabled'.
 * Retorna: void.
 **/
function activarElementos(activar, readonlyIDs = [], disabledIDs = []) {
    
    if (activar) {
        readonlyIDs.forEach(id => document.getElementById(id).removeAttribute('readonly'));
        disabledIDs.forEach(id => document.getElementById(id).removeAttribute('disabled'));
    } else {
        readonlyIDs.forEach(id => document.getElementById(id).setAttribute('readonly', 'true'));
        disabledIDs.forEach(id => document.getElementById(id).setAttribute('disabled', 'true'));
    }

}

// Función listener:
function cerrarSesion(queryString) {

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
}

// Función listener:
function solicitarDatos() {

    const notificaciones = document.getElementById('notificaciones');

    const form = new FormData();
    form.append('accion', 'consultar');
  
    const opciones = {method:'post', body:form};
    
    realizarPeticion('PerfilControlador.php', opciones).then(json => {

        if (typeof json === 'undefined') {
            const mensaje = 'Sus datos no pueden ser cargados de momento.';
            notificaciones.innerHTML = renderizarNotificacion(mensaje, 'alert-warning');
            return;
        }

        //console.log('tengo los dato')

        const datosExtra = {
            "cuatrimestres":json.datos.cuatrimestres,
            "programas":json.datos.programas
        };

        document.getElementById('fotoDePerfil').src =
            'http://localhost/swpra/img/fperfil/predeterminada.png';

        if (json.datos.usuario.dirFoto !== null) {
            document.getElementById('fotoDePerfil').src =
                'http://localhost/swpra/img/fperfil/' + json.datos.usuario.dirFoto;
        }

        

        //cargarEnForm1(json.datos.usuario);
        //cargarEnForm2(json.datos.usuario, datosExtra);
        //cargarEnForm3(json.datos.usuario);

        const urlParams = new URLSearchParams(window.location.search);
        const rol = urlParams.get('rol');

        if (rol !== null && rol === 'estudiante') {
            document.getElementById('escolarEstudiante').classList.remove('d-none');
            document.getElementById('escolarProfesor').classList.add('d-none');
            cargarEstudianteEnInterfaz(json.datos.usuario, datosExtra);
        }

        if (rol !== null && rol === 'profesor') {
            document.getElementById('escolarEstudiante').classList.add('d-none');
            document.getElementById('escolarProfesor').classList.remove('d-none');
            cargarProfesorEnInterfaz(json.datos.usuario, datosExtra);
        }

    });

}

// Función listener:
function solicitarActualizacion(e = SubmitEvent) {

    const notificaciones = document.getElementById('notificaciones');

    const datosEnviar = new FormData(e.target);
    datosEnviar.append('accion', 'actualizar');
    datosEnviar.append('form', e.target.id);

    const opciones = {method:'post', body:datosEnviar};

    realizarPeticion('PerfilControlador.php', opciones)
        .then(json => {

            if (typeof json !== 'undefined') {
                console.log(json);
                if (json.ok) {
                    notificaciones.innerHTML = renderizarNotificacion(json.mensaje, 'alert-success');
                    solicitarDatos();
                    return;
                }
                notificaciones.innerHTML = renderizarNotificacion(json.mensaje, 'alert-warning');
            }

        });

}

function crearFormulario(extras = [], formBase) {
    const formulario = new FormData(formBase);
    extras.forEach((extra) => formulario.append(extra.llave, extra.valor));
    return formulario;
}

function renderizarSalida(contenedor = HTMLElement) {
    contenedor.innerHTML = `
        <div class="h2 text-center">SALIENDO...</div>
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    contenedor.classList.remove('d-none');
}