<?php

class Programa implements JsonSerializable {

    private $id;
    private $nombre;
    private $abreviacion;

    public function __construct() {
    }

    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getAbreviacion() {
        return $this->abreviacion;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setAbreviacion($abreviacion) {
        $this->abreviacion = $abreviacion;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return array(
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'abreviacion' => $this->abreviacion
        );
    }

}

?>