/*function renderizarBotones(posicionActual, contenedorBtn1, contenedorBtn2) {
    let idBotones = {};
    if (posicionActual === 1) { // Botones 'cancelar' y 'comprobar' disponibles
        contenedorBtn1.innerHTML = '<button type="button" class="btn btn-danger" name="btnCancelar" id="btnCancelar">Cancelar</button>';
        contenedorBtn2.innerHTML = '<button type="submit" class="btn btn-primary" name="btnComprobar" id="btnComprobar">Comprobar</button>';
        idBotones = {btn1:'btnCancelar', btn2:'btnComprobar'};
    }
    if (posicionActual === 2) { // Botones 'atrás' y 'siguiente' disponibles
        contenedorBtn1.innerHTML = '<button type="button" class="btn btn-primary d-none" name="btnAtras" id="btnAtras">Atrás</button>';
        contenedorBtn2.innerHTML = '<button type="button" class="btn btn-primary d-none" name="btnSiguiente" id="btnSiguiente">Siguiente</button>';
        idBotones = {btn1:'btnAtras', btn2:'btnSiguiente'};
    }
    if (posicionActual === 3) { // Botones 'atrás' y 'Finalizar' disponibles
        contenedorBtn1.innerHTML = '<button type="button" class="btn btn-primary d-none" name="btnAtras" id="btnAtras">Atrás</button>';
        contenedorBtn2.innerHTML = '<button type="submit" class="btn btn-primary d-none" name="btnFinalizar" id="btnFinalizar">Finalizar</button>';
        idBotones = {btn1:'btnAtras', btn2:'btnFinalizar'};
    }
    return idBotones;
}*/

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

window.addEventListener('hashchange', () => listenerFormRegistro());
window.addEventListener('load', () => listenerFormRegistro());