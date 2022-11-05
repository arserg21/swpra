<?php

include_once("UsuarioNormal.php");

class Estudiante extends UsuarioNormal implements JsonSerializable {

    private $id; // int
    private $matricula; // string(10)
    private $idCuatrimestre; // int

    public function __construct() {
        parent::__construct();
    }

    public function getId() {
        return $this->id;
    }

    public function getMatricula() {
        return $this->matricula;
    }

    public function getIdCuatrimestre() {
        return $this->idCuatrimestre;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setMatricula($matricula) {
        $this->matricula = $matricula;
    }

    public function setIdCuatrimestre($idCuatrimestre) {
        $this->idCuatrimestre = $idCuatrimestre;
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
            'matricula'      => $this->matricula,
            'idCuatrimestre' => $this->idCuatrimestre
        );
        
    }


}

?>