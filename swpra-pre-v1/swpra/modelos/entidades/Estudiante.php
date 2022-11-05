<?php

include_once("UsuarioNormal.php");

class Estudiante extends UsuarioNormal {

    public const ID        = 'id';
    public const MATRICULA = 'matricula';
    public const ID_CUATRI = 'idcuatrimestre';

    private $id; // int
    private $matricula; // string(10)
    private $idCuatrimestre; // int

    /*public function __construct() {
        parent::__construct();
        $this->id = 0;
        $this->matricula = null;
        $this->idCuatrimestre = null;
    }*/

    public function __construct($datos = array()) {
        parent::__construct($datos);
        if (array_key_exists(Estudiante::ID, $datos)) {
            $this->id = $datos[''.Estudiante::ID.''];
        }
        if (array_key_exists(Estudiante::MATRICULA, $datos)) {
            $this->matricula = $datos[''.Estudiante::MATRICULA.''];
        }
        if (array_key_exists(Estudiante::ID_CUATRI, $datos)) {
            $this->idCuatrimestre = $datos[''.Estudiante::ID_CUATRI.''];
        }
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

    public function setId() {
        $this->id = $id;
    }

    public function setMatricula($matricula) {
        $this->matricula = $matricula;
    }

    public function setIdCuatrimestre($idCuatrimestre) {
        $this->idCuatrimestre = $idCuatrimestre;
    }


}

?>