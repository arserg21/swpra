<?php


require_once('entidades/Cuatrimestre.php');

class CuatrimestreModelo {

    private const TABLA = 'cat_cuatrimestres';
    private const PRIMARY_KEY = 'id';

    private const COL_ID  = 'id';
    private const COL_NUM = 'numero';

    public function consultarPorID($id, &$bd) {

        $cuatrimestre = null;
        $sql = sprintf('SELECT * FROM %s WHERE %s = ?', self::TABLA, self::PRIMARY_KEY);

        $sentencia = $bd->prepare($sql);
        $sentencia->bindValue(1, $id, PDO::PARAM_INT);

        if ($sentencia->execute() && $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $registro = $datos[0];
            $cuatrimestre = new Cuatrimestre();
            $cuatrimestre->setId($registro[self::COL_ID]);
            $cuatrimestre->setNumero($registro[self::COL_NUM]);
        }

        return $cuatrimestre;

    }

    public function consultarTodo(&$bd) {

        $cuatrimestres = array();
        $sql = sprintf('SELECT * FROM %s', self::TABLA);

        $sentencia = $bd->prepare($sql);

        if ($sentencia->execute() && $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $cuatrimestre = null;
            foreach ($datos as $registro) {
                $cuatrimestre = new Cuatrimestre();
                $cuatrimestre->setId($registro[self::COL_ID]);
                $cuatrimestre->setNumero($registro[self::COL_NUM]);
                $cuatrimestres[] = $cuatrimestre;
            }
        }

        return $cuatrimestres;

    }

}

?>