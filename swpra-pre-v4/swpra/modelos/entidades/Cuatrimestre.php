<?php

class Cuatrimestre implements JsonSerializable {

    private $id;
    private $numero;

    public function __construct() {
    }

    public function getId() {
        return $this->id;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return array(
            'id'     => $this->id,
            'numero' => $this->numero
        );
    }

}

?>