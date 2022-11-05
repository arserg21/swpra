<?php

include_once("UsuarioNormal.php");

class Profesor extends UsuarioNormal {

    public const ID     = 'id';
    public const CEDULA = 'cedula';

    private $id; // int
    private $cedula; // string(10)

    public function __construct($datos = array()) {
        parent::__construct($datos);
        if (array_key_exists(Profesor::ID, $datos)) {
            $this->id = $datos[''.Profesor::ID.''];
        }
        if (array_key_exists(Profesor::CEDULA, $datos)) {
            $this->cedula = $datos[''.Profesor::CEDULA.''];
        }
    }

    

    public function getId() {
        return $this->id;
    }

    public function getCedula() {
        return $this->cedula;
    }

    public function setId() {
        $this->id = $id;
    }

    public function setCedula($cedula) {
        $this->cedula = $cedula;
    }
}

?>