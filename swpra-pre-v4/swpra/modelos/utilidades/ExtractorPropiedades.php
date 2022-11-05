<?php

final class ExtractorPropiedades {

    //private $arrayObjetivo;

    public function __construct() {
        //$this->arrayObjetivo = $arrayObjetivo;
    }

    /**
     * Retorna un array asociativo para la construcción de objetos:
     */
    public static function extraerDe($arrayObjetivo, $aliasBD, $aliasObjeto) {
        $campos = array();
        foreach ($arrayObjetivo as $cursor => $registro) {
            foreach ($aliasBD as $key => $value) {
                if (array_key_exists($value, $registro)) {
                    $campos[''.$aliasObjeto[$key].''] = $registro[''.$value.''];
                }
            }
        }
        return $campos;
    }

}

?>