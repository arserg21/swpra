<?php

include_once("Usuario.php");

abstract class UsuarioNormal extends Usuario {

    public const ROL      = 'rol';
    public const FE_NAC   = 'fnacimiento';
    public const SEXO     = 'sexo';
    public const DIR_FOTO = 'dirfoto';
    public const PROG_EDU = 'progedu';

    protected $rol; // enum: string
    protected $fechaNacimiento; // string: date
    protected $sexo; // enum: string
    protected $direccionFoto; // string
    protected $programaEducativo; // int: string

    /*public function __construct() {
        parent::__construct();
        $this->rol = null;
        $this->fechaNacimiento = null;
        $this->sexo = null;
        $this->direccionFoto = null;
        $this->programaEducativo = null;
    }*/

    public function __construct($datos = array()) {
        parent::__construct($datos);
        if (array_key_exists(UsuarioNormal::ROL, $datos)) {
            $this->rol = $datos[''.UsuarioNormal::ROL.''];
        }
        if (array_key_exists(UsuarioNormal::FE_NAC, $datos)) {
            $this->fechaNacimiento = $datos[''.UsuarioNormal::FE_NAC.''];
        }
        if (array_key_exists(UsuarioNormal::SEXO, $datos)) {
            $this->sexo = $datos[''.UsuarioNormal::SEXO.''];
        }
        if (array_key_exists(UsuarioNormal::DIR_FOTO, $datos)) {
            $this->direccionFoto = $datos[''.UsuarioNormal::DIR_FOTO.''];
        }
        if (array_key_exists(UsuarioNormal::PROG_EDU, $datos)) {
            $this->programaEducativo = $datos[''.UsuarioNormal::PROG_EDU.''];
        }
    }

    public function getRol() {
        return $this->rol;
    }

    public function getFechaNacimiento() {
        return $this->fechaNacimiento;
    }

    public function getSexo() {
        return $this->sexo;
    }

    public function getDirFoto() {
        return $this->direccionFoto;
    }

    public function getPrograma() {
        return $this->programaEducativo;
    }

    public function setRol($rol) {
        $this->rol = $rol;
    }

    public function setFechaNacimiento($fechaNacimiento) {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function setSexo($sexo) {
        $this->sexo = $sexo;
    }

    public function settDirFoto($direccionFoto) {
        $this->direccionFoto = $direccionFoto;
    }

    public function setPrograma($programaEducativo) {
        $this->programaEducativo = $programaEducativo;
    }

}

?>