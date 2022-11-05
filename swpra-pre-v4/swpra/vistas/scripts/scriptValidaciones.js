
const regexEmail      = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
const regexNombre     = /^(?=.{3,18}$)[a-zñA-ZÑ](\s?[a-zñA-ZÑ])*$/i;
const regexPaterno    = /^(?=.{3,18}$)[a-zñA-ZÑ](\s?[a-zñA-ZÑ])*$/i;
const regexMaterno    = /^(?=.{3,18}$)[a-zñA-ZÑ](\s?[a-zñA-ZÑ])*$/i;
const regexMatricula  = /^[A-Z]{3}O19[0-9]{4}$/;
const regexContrasena = /^(?=.{8,32}$)[a-zA-Z0-9](\s?[a-zA-Z0-9])*$/i

function validarEmail(input = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    if (regexEmail.test(input.value)) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Correo electrónico no válido.</span>';
}

function validarNombre(input = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    console.log('se evaluará: ' + input.value);
    if (regexNombre.test(input.value)) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    console.log('no valido');
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Nombre no válido.</span>';
}

function validarPaterno(input = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    if (regexPaterno.test(input.value)) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Apellido paterno no válido.</span>';
}

function validarMaterno(input = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    if (regexMaterno.test(input.value) || input.value.length === 0) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Apellido materno no válido.</span>';
}

function validarMatricula(input = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    if (regexMatricula.test(input.value)) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Matrícula no válida.</span>';
}

function validarContrasena(input = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    if (regexContrasena.test(input.value)) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Contrasena no válida.</span><br/><span class="">Puedes usar números, letras y espacios en blanco (solo intermedios).</span>';
}

function validarContrasenas(input1 = HTMLInputElement, input2 = HTMLInputElement, notificaciones = HTMLDivElement, btnBloquear = HTMLButtonElement) {
    if (input1.value === input2.value) {
        btnBloquear.removeAttribute('disabled');
        notificaciones.innerHTML = '';
        return;
    }
    btnBloquear.disabled = true;
    notificaciones.innerHTML = '<span class="text-danger">Las contraseñas no coinciden.</span>';
}

function validarEmailO(elemento, etiqueta, boton) {
    
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

function validarConstrasenaO(elemento1, elemento2, etiqueta, boton) {
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