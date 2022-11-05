<?php
// SE INCLUYEN LAS RUTAS DE LAS CLASES A UTILIZAR
include_once("./../modelos/ConexionModelo.php");
include_once("./../modelos/EstudianteModelo.php");
include_once("./../modelos/ProfesorModelo.php");
include_once("./../modelos/AdministradorModelo.php");
include_once("./../modelos/entidades/Estudiante.php");
include_once("./../modelos/entidades/Profesor.php");
include_once("./../modelos/entidades/Administrador.php");
require_once('Utilidades.php');
?>

<?php

$pjson = new PaqueteJSON();
/*$_POST['accion'] = 'autenticar';
$_POST['correoElectronico'] = 'soyadministrador@mail.com';
$_POST['contrasena'] = 'admin123';*/

try {

    array_key_exists('accion', $_POST) or
        throw new Exception('Acción no reconocida.');
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'autenticar':

            // Recuperar email y contraseña:
            $claves = array('correoElectronico', 'contrasena');
            POSTClass::buscar($claves) or
                throw new Exception('Los datos no fueron recibidos por el servidor.');
            $campos = POSTClass::recuperar($claves);

            // Válidar la dirección email:
            Validador::validarEmail($campos['correoElectronico']);
            
            // Crear objetos necesarios:
            $modelos = array(new EstudianteModelo(), new ProfesorModelo(), new AdministradorModelo());
            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_BASICA);
            
            $usuario = new Estudiante();
            $usuario->setEmail($campos['correoElectronico']);
            $usuario->setContrasena($campos['contrasena']);

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
                        if ($modelo instanceof ($a = 'AdministradorModelo')) {
                            $pjson->setDatos(array('autenticado' => true, 'rol' => 'administrador'));
                        } else {
                            $pjson->setDatos(array('autenticado' => true, 'rol' => $usuarioAutenticado->getRol()));
                        }
                        
                        //$pjson->setDatos(array('rol' => $usuarioAutenticado->getRol()));
                        session_start();
                        $_SESSION['usuario'] = $usuarioAutenticado->getEmail();
                        if ($modelo instanceof ($a = 'AdministradorModelo')) {
                            $_SESSION['rol'] = 'administrador';
                        } else {
                            $_SESSION['rol'] = $usuarioAutenticado->getRol();
                        }
                        
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
        default:
            throw new Exception('Acción no reconocida.');
    }

} catch (Exception $e) {

    $pjson->setMensaje($e->getMessage());
    $pjson->setOk(false);
    $pjson->setDatos(null);

} finally {

    print_r(json_encode($pjson));

}