<?php
// SE INCLUYEN LAS RUTAS DE LAS CLASES A UTILIZAR
include_once("./../modelos/ConexionModelo.php");
include_once("./../modelos/EstudianteModelo.php");
include_once("./../modelos/ProfesorModelo.php");
include_once("./../modelos/AdministradorModelo.php");
include_once("./../modelos/entidades/Estudiante.php");
include_once("./../modelos/entidades/Profesor.php");
include_once("./../modelos/entidades/Administrador.php");
include_once("ControladorGenerico.php");
require_once('Utilidades.php');
?>

<?php

$pjson = new PaqueteJSON();
/*$_POST['accion'] = 'autenticar';
$_POST['correoElectronico'] = 'sergio..@mail.com';
$_POST['contrasena'] = 'admin';*/

try {

    array_key_exists('accion', $_POST) or
        throw new Exception('Acción no reconocida.');
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'autenticar':

            // Recuperar email y contraseña:
            $claves = array(
                'correoElectronico' => Usuario::EMAIL,
                'contrasena' => Usuario::CONTRASENA
            );
            POSTClass::buscar($claves) or
                throw new Exception('Los datos no fueron recibidos por el servidor.');
            $datos = POSTClass::recuperar($claves);

            // Válidar la dirección email:
            Validador::validarEmail($datos[''.Usuario::EMAIL.'']);
            
            // Crear objetos necesarios:
            $modelos = array(new EstudianteModelo(), new ProfesorModelo(), new AdministradorModelo());
            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_BASICA);
            $usuario = new Estudiante($datos);
            $usuarioAutenticado = null;
            $existe = false;
            $i = 0;

            // Comprobación:
            foreach ($modelos as $modelo) {
                /*if ($i === 0) {
                    print_r('buscando como estudiante');
                }
                if ($i === 1) {
                    print_r('buscando como profesor');
                }
                if ($i === 2) {
                    print_r('buscando como admin');
                }*/
                if ($modelo->existe($usuario, $bd)) {
                    $existe = true;
                    if ($usuarioAutenticado = $modelo->hacerMatch($usuario, $bd)) {
                        $pjson->setDatos(array('autenticado' => true));
                        session_start();
                        $_SESSION['usuario'] = $usuarioAutenticado->getEmail();
                        break;
                    } else {
                        $pjson->setDatos(array('autenticado' => false));
                        $pjson->setMensaje('Contraseña incorrecta.');
                        break;
                    }
                }
                $i++;
            }

            if (!$existe) {
                $pjson->setDatos(array('autenticado' => false));
                $pjson->setMensaje('La cuenta no existe.');
            }
            

            $pjson->setOk(true);
            
            break;
    }

} catch (Exception $e) {

    $pjson->setMensaje($e->getMessage());
    $pjson->setOk(false);
    $pjson->setDatos(null);

} finally {

    print_r(json_encode($pjson->obtenerDatos()));

}