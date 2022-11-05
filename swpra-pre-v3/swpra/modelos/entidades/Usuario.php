<?php

abstract class Usuario {

    protected $email; // string
    protected $contrasena; // string
    protected $nombre; // string
    protected $apaterno; // string
    protected $amaterno; // string

    public function __construct() {
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

    public function getApaterno() {
        return $this->apaterno;
    }

    public function getAmaterno() {
        return $this->amaterno;
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

    public function setApaterno($apaterno) {
        $this->apaterno = $apaterno;
    }

    public function setAmaterno($amaterno) {
        $this->amaterno = $amaterno;
    }

}

?>