

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
/*$_POST['accion'] = 'registrar';
$_POST['correoElectronico'] = 'arso191194@upemor.edu.mx';
$_POST['nombre'] = 'sergio';
$_POST['paterno'] = 'ayala';
$_POST['materno'] = 'ayala';
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
            $claves = array( // 'clave en POST' => 'nueva clave'
                'nombre' => Estudiante::NOMBRE, 'paterno' => Estudiante::PATERNO,
                'materno' => Estudiante::MATERNO, 'fnacimiento' => Estudiante::FE_NAC,
                'sexo' => Estudiante::SEXO, 'correoElectronico' => Estudiante::EMAIL,
                'contrasena' => Estudiante::CONTRASENA, 'contrasenaConfirmada' => 'confirmada'
            );
            POSTClass::buscar($claves) or
                throw new Exception('Los datos no fueron recibidos por el servidor.');
            $campos = POSTClass::recuperar($claves);

            // Válidar los datos:
            Validador::validarEmail($campos[''.Estudiante::EMAIL.'']);
            Validador::validarNombre($campos[''.Estudiante::NOMBRE.'']);
            Validador::validarApellido(Validador::PATERNO, $campos[''.Estudiante::PATERNO.'']);
            Validador::validarApellido(Validador::MATERNO, $campos[''.Estudiante::MATERNO.'']);
            Validador::validarSexo($campos[''.Estudiante::SEXO.'']);
            Validador::validarContrasena($campos[''.Estudiante::CONTRASENA.''], $campos['confirmada']);

            // Creación de los objetos:
            $preEstudiante = new Estudiante($campos);
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

            $conexion->beginTransaction();
                $modEstudiante->insertarUsuario($preEstudiante, $conexion);
                $modEstudiante->insertarEstudiante($preEstudiante, $conexion);
                $modToken->insertar($token, $conexion);
            $conexion->commit();

            $pjson->setDatos(array('registrado' => true));

            SWPRAEmailer::enviar(
                $campos[''.Estudiante::EMAIL.''],
                $campos[''.Estudiante::NOMBRE.''],
                $token->getToken()
            );

            $pjson->setOk(true);

            session_start();
            $_SESSION['usuario'] = $campos[''.Estudiante::EMAIL.''];

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

    print_r(json_encode($pjson->obtenerDatos()));

}


?>