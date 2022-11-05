<?php

require_once('Utilidades.php');

$pjson = new PaqueteJSON();

try {

    /*(isset($_SESSION) && array_key_exists('usuario', $_SESSION)) or
        throw new Exception('No hay sesión activa.');*/
    
    array_key_exists('accion', $_POST) or
        throw new Exception('Acción no reconocida.');
    $accion = $_POST['accion'];

    switch($accion) {

        case 'verificar':

            $datos = array();
            
            session_start();

            $sesion = isset($_SESSION['usuario']) ? 'activa' : 'ninguna';

            if ($sesion === 'activa') {
                $datos['rol'] = $_SESSION['rol'];
            }

            $datos['sesion'] = $sesion;
            
            $pjson->setDatos($datos);
            $pjson->setOk(true);

            break;
        case 'salir':

            session_start();
            session_unset();
            session_destroy();

            $pjson->setOk(true);

            break;
        default:
            throw new Exception('Acción no reconocida.');
            
    }
} catch (Exception $e) {

    $pjson->setMensaje($e->getMessage());
    $pjson->setOk(false);

} finally {
    print_r(json_encode($pjson));
}

?>