<?php

class Token {

    private $id;
    private $token;
    private $fechaExpiracion;
    private $tipo;
    private $usado;
    private $email;

    public function __construct($id, $token, $fechaExpiracion, $tipo, $usado, $email) {
        $this->id = $id;
        $this->token = $token;
        $this->fechaExpiracion = $fechaExpiracion;
        $this->tipo = $tipo;
        $this->usado = $usado;
        $this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function getToken() {
        return $this->token;
    }

    public function getFechaExpiracion() {
        return $this->fechaExpiracion;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getUsado() {
        return $this->usado;
    }

    public function getEmail() {
        return $this->email;
    }

}

?>