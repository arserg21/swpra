<?php

abstract class Usuario {

    public const EMAIL      = 'email';
    public const CONTRASENA = 'contrasena';
    public const NOMBRE     = 'nombre';
    public const PATERNO    = 'paterno';
    public const MATERNO    = 'materno';

    protected $email; // string
    protected $contrasena; // string
    protected $nombre; // string
    protected $apellidoPaterno; // string
    protected $apellidoMaterno; // string

    /*public function __construct() {
        $this->email = null;
        $this->contrasena = null;
        $this->nombre = null;
        $this->apellidoPaterno = null;
        $this->apellidoMaterno = null;
    }*/

    public function __construct($datos = array()) {
        if (array_key_exists(Usuario::EMAIL, $datos)) {
            $this->email = $datos[''.Usuario::EMAIL.''];
        }
        if (array_key_exists(Usuario::CONTRASENA, $datos)) {
            $this->contrasena = $datos[''.Usuario::CONTRASENA.''];
        }
        if (array_key_exists(Usuario::NOMBRE, $datos)) {
            $this->nombre = $datos[''.Usuario::NOMBRE.''];
        }
        if (array_key_exists(Usuario::PATERNO, $datos)) {
            $this->apellidoPaterno = $datos[''.Usuario::PATERNO.''];
        }
        if (array_key_exists(Usuario::MATERNO, $datos)) {
            $this->apellidoMaterno = $datos[''.Usuario::MATERNO.''];
        }
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContrasena() {
        return $this->contrasena;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellidoPaterno() {
        return $this->apellidoPaterno;
    }

    public function getApellidoMaterno() {
        return $this->apellidoMaterno;
    }

    public function setEmail ($email) {
        $this->email = $email;
    }

    public function setContrasena($contrasena) {
        $this->contrasena = $contrasena;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellidoPaterno($apellidoPaterno) {
        $this->apellidoPaterno = $apellidoPaterno;
    }

    public function setApellidoMaterno($apellidoMaterno) {
        $this->apellidoMaterno = $apellidoMaterno;
    }

}

?>