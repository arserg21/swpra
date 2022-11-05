function renderizarFormInicio() {
    return `
    <form class="form card shadow p-5 mb-3 mx-auto" style="max-width: 400px;" id="formInicio">
        <div class="mb-3">
            <label for="correoElectronico" class="form-label">Dirección de correo electrónico:</label>
            <input type="email" class="form-control" name="correoElectronico" id="correoElectronico">
            <div class="form-text text-danger"  id="estatusCorreo">
            </div>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña:</label>
            <input type="password" class="form-control" name="contrasena" id="contrasena">
            <div class="form-text text-danger" id="estatusContrasena">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" id="btnIngresar">Ingresar</button>
        <div class="mt-5 mb-3 text-center">
            <a href="#formRegistro" class="link-primary" id="vformRegistro">¿No tienes cuenta?</a><br>
            <a href="#formRecupera" class="link-primary" id="vformRecupera">Olvidé mi contraseña</a>
        </div>
    </form>
    `;
}