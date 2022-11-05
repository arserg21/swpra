<?php

include_once("Usuario.php");

abstract class UsuarioNormal extends Usuario {

    protected $rol; // enum: string
    protected $fnacimiento; // string: date
    protected $sexo; // enum: string
    protected $dirFoto; // string
    protected $idPrograma; // int: string

    public function __construct() {
        parent::__construct();
    }

    public function getRol() {
        return $this->rol;
    }

    public function getFnacimiento() {
        return $this->fnacimiento;
    }

    public function getSexo() {
        return $this->sexo;
    }

    public function getDirFoto() {
        return $this->dirFoto;
    }

    public function getIdPrograma() {
        return $this->idPrograma;
    }

    public function setRol($rol) {
        $this->rol = $rol;
    }

    public function setFnacimiento($fnacimiento) {
        $this->fnacimiento = $fnacimiento;
    }

    public function setSexo($sexo) {
        $this->sexo = $sexo;
    }

    public function setDirFoto($dirFoto) {
        $this->dirFoto = $dirFoto;
    }

    public function setIdPrograma($idPrograma) {
        $this->idPrograma = $idPrograma;
    }

}

?>