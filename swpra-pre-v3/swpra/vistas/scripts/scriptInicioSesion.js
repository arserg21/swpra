/*
 * ############################################################################
 * ################### FUNCIONES COMPATIDAS
 * ############################################################################
*/

/*function validarEmail(elemento, etiqueta, boton) {
    var regex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
    if (!regex.test(elemento.value)) {
        etiqueta.innerHTML = "Correo electrónico no válido.";
        if (boton !== undefined) {
            boton.disabled = true;
        }
    } else {
        etiqueta.innerHTML = "";
        if (boton !== undefined) {
            boton.disabled = false;
        }
    }
}*/

function validarEmail2(elemento, etiqueta, boton) {
    var regex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
    if (!regex.test(elemento.value)) {
        etiqueta.innerHTML = '<span class="text-danger">Correo electrónico no válido.</span>';
        if (boton !== undefined) {
            boton.disabled = true;
        }
    } else {
        etiqueta.innerHTML = '';
        if (boton !== undefined) {
            boton.disabled = false;
        }
    }
}

function validarConstrasena(elemento1, elemento2, etiqueta, boton) {
    if (elemento1.value.length >= 8) {
        if (elemento1.value === elemento2.value) {
            // Las contraseñas coinciden y son válidas:
            //etiqueta.innerHTML = '<span class="text-success">Las contraseñas coinciden.</span>';
            etiqueta.innerHTML = '';
            boton.removeAttribute('disabled');
            return;
        } else {
            etiqueta.innerHTML = '<span class="text-danger">Las contraseñas no coinciden.</span>';
        }
    } else {
        etiqueta.innerHTML = '<span class="text-danger">Las contraseña tiene que tener 8 caracteres como mínimo.</span>';
    }
    boton.setAttribute('disabled', 'true');
}

function cambiarEstado(boton = null, esNormal = false, texto = '') {
    if (esNormal) {
        boton.innerHTML = texto;
    } else {
        boton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Ingresando...
        `;
    }
}

/*
 * ############################################################################
 * ################### SCRIPT DEL FORM DE INICIO DE SESIÓN
 * ############################################################################
*/

function renderizarFormInicio() {
    return `
    <form class="form card shadow p-5 mb-3 mx-auto" style="max-width: 400px;" id="formInicio">
        <div class="mb-3">
            <label for="correoElectronico" class="form-label">Dirección de correo electrónico:<span class="text-danger">*</span></label></label>
            <input type="email" class="form-control" name="correoElectronico" id="correoElectronico">
            <div class="form-text"  id="estatusCorreo"></div>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña:<span class="text-danger">*</span></label></label>
            <input type="password" class="form-control" name="contrasena" id="contrasena">
            <div class="form-text text-danger" id="estatusContrasena"></div>
        </div>
        <div class="d-grid gap-1 mb-3">
            <button type="submit" class="btn btn-primary" id="btnIngresar">Ingresar</button>
        </div>
        <div class="text-center mt-3">
            <a href="#formRegistro" class="link-primary" id="vformRegistro">¿No tienes cuenta?</a><br>
            <a href="#formRecupera" class="link-primary" id="vformRecupera">Olvidé mi contraseña</a>
        </div>
    </form>
    `;
}

function listenerFormInicio() {

    if (window.location.hash !== '#formInicio') {
        return;
    }

    const notificaciones = document.getElementById('notificaciones');
    const seccionFormulario = document.getElementById('seccionFormulario');

    if (document.getElementById('formInicio') === null) {
        // Renderizar el form:
        seccionFormulario.innerHTML = renderizarFormInicio();
    }

    document.getElementById('correoElectronico')
        .addEventListener('input', (e) => {
            validarEmail2(e.target,
                document.getElementById('estatusCorreo'),
                document.getElementById('btnIngresar')
            );
        });
    
    document.getElementById('formInicio')
        .addEventListener('submit', (e) => {

            e.preventDefault();
            const formulario = new FormData(e.target);
                  formulario.append('accion', 'autenticar');
            const opciones = {method:'post', body:formulario};

            cambiarEstado(e.submitter, false);

            realizarPeticion('InicioSesionControlador.php', opciones).then(datos => {

                //console.log(datos);

                if (typeof datos === 'undefined') {
                    cambiarEstado(e.submitter, true, 'Ingresar');
                    return;
                }
                
                if (datos.ok && datos.datos.autenticado === true) {
                    setTimeout((() => window.location.reload()), 3000);
                } else {
                    cambiarEstado(e.submitter, true, 'Ingresar');
                }

                if (datos.mensaje !== '') {
                    notificaciones.innerHTML = renderizarNotificacion(datos.mensaje, 'alert-warning');
                }

            });

    });
}

/*
 * ############################################################################
 * ################### SCRIPT DEL FORM DE INICIO DE REGISTRO
 * ############################################################################
*/

function renderizarFormRegistro() {
    return `
        <form class="form card shadow py-3 px-5 mb-3 mx-auto" style="max-width: 500px; max-height: 75vh;" id="formRegistro">
            <section name="encabezadoForm" id="encabezadoForm">
                <div class="border-bottom text-center mb-3 pb-2">
                    <h4 class="text-primary">Proceso de registro</h4>
                </div>
                <div class="progress" name="progreso" id="progreso">
                    <div name="barra" id="barra" class="progress-bar progress-bar-stripeddd progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </section>
            <section class="overflow-auto mt-3" name="datos" id="datos">
                <div class="row d-none mx-auto" name="parte" id="parte1">
                    <div class="form-floating mb-3 g-1">
                        <input type="email" class="form-control" name="correoElectronico" id="correoElectronico" placeholder="Dirección de correo electrónico: *" required>
                        <label for="correoElectronico">Dirección de correo electrónico: <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="row d-none mx-auto" name="parte" id="parte2">
                    <div class="form-floating mb-3 g-1">
                        <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Contraseña: *">
                        <label for="contrasena">Contraseña: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-floating mb-3 g-1">
                        <input type="password" class="form-control" name="contrasenaConfirmada" id="contrasenaConfirmada" placeholder="Nuevamente ingrese su contraseña: *">
                        <label for="contrasenaConfirmada">Vuelva a ingresar su contraseña: <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="row d-none mx-auto" name="parte" id="parte3">
                    <div class="form-floating mb-3 g-1">
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre: *">
                        <label for="nombre">Nombre: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-floating mb-3 g-1">
                        <input type="text" class="form-control" name="paterno" id="paterno" placeholder="Apellido paterno: *">
                        <label for="paterno">Apellido paterno: <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-floating mb-3 g-1">
                        <input type="text" class="form-control" name="materno" id="materno" placeholder="Apellido materno: *">
                        <label for="materno">Apellido materno:</label>
                    </div>
                    <div class="form-floating mb-3 g-1">
                        <input type="date" class="form-control" name="fnacimiento" id="fnacimiento" placeholder="Fecha de nacimiento: *">
                        <label for="fnacimiento">Fecha de nacimiento: <span class="text-danger">*</span></label>
                    </div>
                    <div class="flex-row d-md-flex justify-content-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sexo" id="sexo1" value="hombre">
                            <label class="form-check-label" for="sexo1">Hombre</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sexo" id="sexo2" value="mujer">
                            <label class="form-check-label" for="sexo2">Mujer</label>
                        </div>
                    </div>
                </div>
            </section>
            <section class="form-text mb-3" name="mensajesForm" id="mensajesForm">
            </section>
            <section class="d-flex flex-row justify-content-between mb-3" name="navegacionForm" id="navegacionForm">
                <div class="text-center" name="contenedorBtn1" id="contenedorBtn1">
                    <button type="button" class="btn btn-danger" name="btnCancelar" id="btnCancelar">Cancelar</button>
                    <button type="button" class="btn btn-primary d-none" name="btnAtras" id="btnAtras">Atrás</button>    
                </div>
                <div class="text-center" name="contenedorBtn2" id="contenedorBtn2">
                    <button type="submit" class="btn btn-primary" name="btnComprobar" id="btnComprobar" disabled>Comprobar</button>
                    <button type="button" class="btn btn-primary d-none" name="btnSiguiente" id="btnSiguiente" disabled>Siguiente</button>
                    <button type="submit" class="btn btn-primary d-none" name="btnFinalizar" id="btnFinalizar">Finalizar</button>
                </div>
            </section>
        </form>
    `;
}

function actualizarBarra(barra, porcentaje) {
    barra.ariaValueNow = `${porcentaje}`;
    barra.style.width = `${porcentaje}%`;
    barra.innerHTML = `${porcentaje}%`
}

// Función que muestra / oculta secciones del formulario:
function* mostrar(nombreGrupo) {
    let posicion = 1;
    let parametros = {};
    let detener = false;
    const secciones = document.querySelectorAll(`div[name='${nombreGrupo}']`);
    while (!detener) {
        secciones.forEach((element) => {
            if (element.id === ('parte'+posicion)) {
                //console.log('se mostrará la parte ' + posicion);
                //console.log(element);
                element.classList.remove('d-none');
            } else if (!element.classList.contains('d-none')) {
                element.classList.add('d-none');
            }
        });
        parametros = yield posicion;
        while (posicion + parametros.desplazamiento === 0 ||
               posicion + parametros.desplazamiento === 4) {
            parametros = yield posicion;
        }
        posicion += parametros.desplazamiento;
        detener = parametros.detener;
    }
}

function listenerFormRegistro() {

    if (window.location.hash !== '#formRegistro') {
        return;
    }

    const notificaciones = document.getElementById('notificaciones');
    const seccionFormulario = document.getElementById('seccionFormulario');

    // Cargar el formulario sí no existe:
    if (document.getElementById('formRegistro') === null) {
        seccionFormulario.innerHTML = renderizarFormRegistro();
    }

    const formulario = document.getElementById('formRegistro');

    //const contenedorBtn1 = document.getElementById('contenedorBtn1');
    //const contenedorBtn2 = document.getElementById('contenedorBtn2');

    const btnCancelar  = document.getElementById('btnCancelar');
    const btnAtras     = document.getElementById('btnAtras');
    const btnComprobar = document.getElementById('btnComprobar');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnFinalizar = document.getElementById('btnFinalizar');

    const barra = document.getElementById('barra');

    const inputEmail       = document.getElementById('correoElectronico');
    const inputContrasena1 = document.getElementById('contrasena');
    const inputContrasena2 = document.getElementById('contrasenaConfirmada');
    const mensajesForm     = document.getElementById('mensajesForm');
    
    // Mostrar la parte inicial del formulario:
    const generador = mostrar('parte');
          generador.next();
    setTimeout(() => actualizarBarra(barra, 33), 500);

    inputEmail.addEventListener('input', function(e) {
        validarEmail2(e.target, mensajesForm, btnComprobar);
    });

    inputContrasena1.addEventListener('input', function(e) {
        validarConstrasena(e.target, inputContrasena2, mensajesForm, btnSiguiente);
    });

    inputContrasena2.addEventListener('input', function(e) {
        validarConstrasena(e.target, inputContrasena1, mensajesForm, btnSiguiente);
    });

    btnCancelar.addEventListener('click', function(e) {
        //console.log('fgeneradora finalizada?: '+ generador.next({desplazamiento:0, detener:true}).done);
        window.location.hash = 'formInicio';
    });

    btnAtras.addEventListener('click', function(e) {
        const posicion = generador.next({desplazamiento: -1, detener:false}).value;
        if (posicion === 1) {
            btnAtras.classList.add('d-none');
            btnSiguiente.classList.add('d-none');
            btnCancelar.classList.remove('d-none');
            btnComprobar.classList.remove('d-none');
        }
        if (posicion === 2) {
            btnFinalizar.classList.add('d-none');
            btnSiguiente.classList.remove('d-none');
        }
        actualizarBarra(barra, 33*posicion);
    });

    btnSiguiente.addEventListener('click', function(e) {
        const posicion = generador.next({desplazamiento: 1, detener:false}).value;
        //console.log(posicion);
        if (posicion === 2) {
            btnCancelar.classList.add('d-none');
            btnComprobar.classList.add('d-none');
            btnAtras.classList.remove('d-none');
            btnSiguiente.classList.remove('d-none');
        }
        if (posicion === 3) {
            btnSiguiente.classList.add('d-none');
            btnFinalizar.classList.remove('d-none');
        }
        actualizarBarra(barra, 33*posicion);
    });

    // Agregar un evento en el cual se realiza la petición para registrarse:
    formulario.addEventListener('submit', function(e) {

        e.preventDefault();

        if (e.submitter === btnComprobar) {
            // Solicitud de comprobación de correo electrónico:
            const paquete = new FormData(e.target);
                  paquete.append('accion', 'comprobar');
            realizarPeticion('RegistroControlador.php', {method:'post', body:paquete}).then(json => {

                console.log(json);

                if (json.ok) {
                    if (json.datos.existe === false) {
                        btnSiguiente.removeAttribute('disabled');
                        btnSiguiente.click();
                        btnSiguiente.setAttribute('disabled', 'true');
                    }
                }

                if (json.mensaje !== '') {
                    notificaciones.innerHTML = renderizarNotificacion(json.mensaje, 'alert-info');
                }
                

            });
        }

        if (e.submitter === btnFinalizar) {
            // Solicitud de registro:
            const paquete = new FormData(e.target);
                  paquete.append('accion', 'registrar');
            const opciones = {method:'post', body:paquete};
            realizarPeticion('RegistroControlador.php', opciones).then(json => {
                if (json.ok && json.datos.registrado === true) {
                    setTimeout((() => window.location.reload()), 3000);
                }
                if (json.mensaje !== '') {
                    notificaciones.innerHTML = renderizarNotificacion(json.mensaje, 'alert-info');
                }
            });
        }
        
    });
}

/*
 * ############################################################################
 * ################### SCRIPT DEL FORM DE INICIO DE RECUPERA
 * ############################################################################
*/

function renderizarFormRecupera() {

}

function listenerFormRecupera() {

}

/*
 * ############################################################################
 * ################### AGREGACIÓN DE EVENTOS
 * ############################################################################
*/

window.addEventListener('hashchange', () => listenerFormInicio());
window.addEventListener('hashchange', () => listenerFormRegistro());
window.addEventListener('hashchange', () => listenerFormRecupera());

window.addEventListener('load', () => listenerFormInicio());
window.addEventListener('load', () => listenerFormRegistro());
window.addEventListener('load', () => listenerFormRecupera());