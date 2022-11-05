<?php

require_once('Utilidades.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sesion = isset($_SESSION['usuario']) ? 'activa' : 'ninguna';

$pjson = new PaqueteJSON();
$pjson->setDatos(array('sesion' => $sesion));
$pjson->setOk(true);

print_r(json_encode($pjson->obtenerDatos()));

?>