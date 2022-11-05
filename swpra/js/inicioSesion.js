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
            validarEmail(e.target,
                document.getElementById('estatusCorreo'),
                document.getElementById('btnIngresar')
            );
        });
    
    document.getElementById('formInicio')
        .addEventListener('submit', (e) => {

            e.preventDefault();
            const formulario = new FormData(e.target);
                  formulario.append('accion', 'autenticar');
            const opciones = {method:'post', body:formulario}

            realizarPeticion('InicioSesionControlador.php', opciones).then(datos => {

                if (datos.ok && datos.datos.autenticado === true) {
                    setTimeout((() => window.location.reload()), 3000);
                }

                if (datos.mensaje !== '') {
                    notificaciones.innerHTML = renderizarNotificacion(datos.mensaje, 'alert-warning');
                }

            });
    });
}

function listenerFormRecupera() {

}

window.addEventListener('hashchange', () => listenerFormInicio());
window.addEventListener('hashchange', () => listenerFormRecupera());

window.addEventListener('load', () => listenerFormInicio());
window.addEventListener('load', () => listenerFormRecupera());

function validarEmail(elemento, etiqueta, boton) {
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
}

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

/**/