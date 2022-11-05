

<?php
// SE INCLUYEN LAS RUTAS DE LAS CLASES A UTILIZAR
require_once('../modelos/entidades/Usuario.php');
require_once('../modelos/entidades/UsuarioNormal.php');
require_once('../modelos/entidades/Estudiante.php');
require_once('../modelos/entidades/Estudiante.php');
require_once('../modelos/entidades/Token.php');
require_once('../modelos/EstudianteModelo.php');
require_once('../modelos/TokenModelo.php');
require_once('../modelos/ConexionModelo.php');
require_once('Utilidades.php');
require_once('EnviarEmailControlador.php');
?>

<?php

$pjson = new PaqueteJSON();
$banderaTransaccion = false;

/*$_POST['accion'] = 'comprobar';
$_POST['correoElectronico'] = 'noexiste@upemor.edu.mx';*/


/*$_POST['accion'] = 'registrar';
$_POST['correoElectronico'] = 'noexiste@upemor.edu.mx';
$_POST['nombre'] = 'sergio';
$_POST['paterno'] = 'ayala';
$_POST['materno'] = '';
$_POST['fnacimiento'] = '2000-10-10';
$_POST['sexo'] = 'hombre';
$_POST['contrasena'] = 'seguridad123';
$_POST['contrasenaConfirmada'] = 'seguridad123';*/

try {

    array_key_exists('accion', $_POST) or
        throw new Exception('Acción no reconocida.');
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'comprobar':

            // Recuperar la dirección email:
            array_key_exists('correoElectronico', $_POST) or
                throw new Exception('Los datos no fueron recibidos por el servidor.');
            $dirEmail = $_POST['correoElectronico'];

            // Válidar la dirección email:
            filter_var($dirEmail, FILTER_VALIDATE_EMAIL)  or
                throw new Exception('Dirección de correo electrónico no válida.');
            
            // Crear objetos necesarios:
            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_BASICA);
            $estudiante = new Estudiante();
            $estudiante->setEmail($dirEmail);
            $modelo = new EstudianteModelo();

            // Comprobación:
            if ($modelo->existe($estudiante, $bd)) {
                $pjson->setMensaje('La dirección de correo electrónico ya pertenece a una cuenta.');
                $pjson->setDatos(array('existe' => true));
            } else {
                $pjson->setDatos(array('existe' => false));
            }

            $pjson->setOk(true);
            
            break;
        case 'registrar':

            // Recuperar los campos:
            $claves = array('nombre', 'paterno', 'materno', 'fnacimiento',
                'sexo', 'correoElectronico','contrasena', 'contrasenaConfirmada'
            );
            POSTClass::buscar($claves) or
                throw new Exception('Los datos no fueron recibidos por el servidor.');
            $campos = POSTClass::recuperar($claves);

            // Válidar los datos:
            Validador::validarEmail($campos['correoElectronico']);
            Validador::validarNombre($campos['nombre']);
            Validador::validarApellido(Validador::PATERNO, $campos['paterno']);
            Validador::validarApellido(Validador::MATERNO, $campos['materno']);
            Validador::validarSexo($campos['sexo']);
            Validador::validarContrasena($campos['contrasena'], $campos['contrasenaConfirmada']);

            // Creación de los objetos:
            $preEstudiante = new Estudiante();
            $preEstudiante->setEmail($campos['correoElectronico']);
            $preEstudiante->setContrasena($campos['contrasena']);
            $preEstudiante->setNombre($campos['nombre']);
            $preEstudiante->setApaterno($campos['paterno']);
            $preEstudiante->setAmaterno($campos['materno']);
            $preEstudiante->setFnacimiento($campos['fnacimiento']);
            $preEstudiante->setSexo($campos['sexo']);

            $conexion = ConexionModelo::getConexion(ConexionModelo::CONEXION_BASICA);
            $modEstudiante = new EstudianteModelo();

            // Comprobaciones:
            if ($modEstudiante->existe($preEstudiante, $conexion)) {
                throw new Exception('La dirección de correo electrónico ya esta registrada.');
            }

            // Creación de objetos:
            $modToken = new TokenModelo();
            $token = new Token(
                0, Utilidades::generarToken(), null,
                "verificacion", "no", $preEstudiante->getEmail()
            );

            //throw new Exception('todo bien, pero no te voy a registrar');

            // Inicio de la transacción:

            $banderaTransaccion = true;

            /*$conexion->beginTransaction();
                $modEstudiante->insertarUsuario($preEstudiante, $conexion);
                $modEstudiante->insertarEstudiante($preEstudiante, $conexion);
                $modToken->insertar($token, $conexion);
            $conexion->commit();*/

            $pjson->setDatos(array('registrado' => true));

            /*SWPRAEmailer::enviar(
                $campos[Estudiante::EMAIL],
                $campos[Estudiante::NOMBRE],
                $token->getToken()
            );*/

            $pjson->setOk(true);

            session_start();
            $_SESSION['usuario'] = $campos['correoElectronico'];

            break;
    }

} catch (Exception $e) {

    if ($banderaTransaccion) {
        $conexion->rollback();
        $pjson->setMensaje('De momento su registro no puede ser completado.');
    }

    $pjson->setMensaje($e->getMessage());

    $pjson->setOk(false);
    $pjson->setDatos(null);

} finally {

    print_r(json_encode($pjson));

}


?>