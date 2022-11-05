<?php

require_once('entidades/Programa.php');

class ProgramaModelo {

    private const TABLA = 'cat_programas';
    private const PRIMARY_KEY = 'id';

    private const COL_ID = 'id';
    private const COL_NOMBRE = 'nombre';
    private const COL_ABREV = 'abreviacion';

    public function __construct() {
    } 

    public function consultarPorID($id, &$bd) {

        $programa = null;
        $sql = sprintf('SELECT * FROM %s WHERE %s = ?', self::TABLA, self::PRIMARY_KEY);

        $sentencia = $bd->prepare($sql);
        $sentencia->bindValue(1, $id, PDO::PARAM_INT);

        if ($sentencia->execute() && $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $registro = $datos[0];
            $programa = new Programa();
            $programa->setId($registro[self::COL_ID]);
            $programa->setNombre($registro[self::COL_NOMBRE]);
            $programa->setAbreviacion($registro[self::COL_ABREV]);
        }

        return $programa;

    }

    public function consultarTodo(&$bd) {

        $programas = array();

        $sql = sprintf('SELECT * FROM %s', self::TABLA);

        $sentencia = $bd->prepare($sql);

        if ($sentencia->execute() && $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $programa = null;
            foreach($datos as $registro) {
                $programa = new Programa();
                $programa->setId($registro[self::COL_ID]);
                $programa->setNombre($registro[self::COL_NOMBRE]);
                $programa->setAbreviacion($registro[self::COL_ABREV]);
                $programas[] = $programa;
            }
            
        }

        return $programas;

    }
    
}

?>