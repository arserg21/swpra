<?php

include_once("Usuario.php");

class Administrador extends Usuario {
    public function __construct($datos = array()) {
        parent::__construct($datos);
    }
}

?>