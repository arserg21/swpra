<?php

include_once("UsuarioNormal.php");

class Profesor extends UsuarioNormal implements JsonSerializable {

    private $id; // int
    private $cedula; // string(10)

    public function __construct() {
        parent::__construct();
    }

    public function getId() {
        return $this->id;
    }

    public function getCedula() {
        return $this->cedula;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCedula($cedula) {
        $this->cedula = $cedula;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {

        return array(
            'email'          => $this->email,
            'nombre'         => $this->nombre,
            'paterno'        => $this->apaterno,
            'materno'        => $this->amaterno,

            'rol'            => $this->rol,
            'fnacimiento'    => $this->fnacimiento,
            'sexo'           => $this->sexo,
            'dirFoto'        => $this->dirFoto,
            'idPrograma'     => $this->idPrograma,

            'id'             => $this->id,
            'cedula'         => $this->cedula,
        );
        
    }
}

?>