<?php

/*interface ActualizacionFlexible {

    public function afAgregarCampo($campo, $valor, $tipo); // :void
    public function afSetWhere($campo, $valor, $tipo); // void
    public function afConstruirSql($tabla); // :void
    public function afBindAll(&$bd); // :PDOStatement
    
    public function afActualizar(&$bd); // :void

}*/

abstract class Actualizador /*implements ActualizacionFlexible*/ {

    // Arrays para la actualización flexible
    protected $campos  = array();
    protected $valores = array();
    protected $tipos   = array();
    protected $where   = array();

    private $reporteActualizacion = '';

    protected $sql  = '';

    //public $respaldo = '';

    public final function agregarActualizacion($campo, $valor, $tipo) {
        $this->campos[]  = $campo;
        $this->valores[] = $valor;
        $this->tipos[]   = $tipo;
    }

    public final function setClausulaWhere($campo, $valor, $tipo) {
        $this->where['campo'] = $campo;
        $this->where['valor'] = $valor;
        $this->where['tipo']  = $tipo;
    }

    public final function construirSql($tabla) {
        $this->sql = "UPDATE $tabla SET ";
        $bandera = 0;
        // Agrega los campos:
        foreach($this->campos as $campo) {
            $bandera === 0 ? $this->sql .= "$campo = ?" : $this->sql .= ", $campo = ?";
            $bandera = 1;
        }
        // Agrega la clausula 'where':
        $this->sql .= sprintf(" WHERE %s = ?", $this->where['campo']);
        // Limpiar:
        $this->campos = array();
    }

    private function bindAll(&$bd) {
        // Quita el primer elemento y lo almacena en $sql:
        $sentencia = $bd->prepare($this->sql);
        // Pasar todos los valores:
        $index = 0;
        foreach($this->valores as $valor) {
            $sentencia->bindValue(($index+1), $valor, $this->tipos[$index]);
            $index++;
        }
        // Establece el valor de la clausula where:
        $sentencia->bindValue($index+1, $this->where['valor'], $this->where['tipo']);
        $this->valores = array();
        $this->tipos = array();
        $this->where = array();
        $this->sql = '';
        return $sentencia;
    }

    public final function actualizar(&$bd) {
        $this->getReporteActualizacion = '';
        $sentencia = self::bindAll($bd);
        $ejecutada = $sentencia->execute();
        if (!$ejecutada) {
           $this->reporteActualizacion = 'Actualización no realizada: ';
           return false;
        } else {
            $this->reporteActualizacion = sprintf('Actualización realizada: %d registro(s) afectado(s).', $sentencia->rowCount());
            return $sentencia->rowCount() > 0;
        }
    }

    public final function getReporteActualizacion() {
        return $this->reporteActualizacion;
    }

}

?>