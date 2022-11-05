<?php

class ConexionModelo {

    private const url = "mysql:host=localhost;dbname=bd_swpra";

    public const CONEXION_BASICA = 0;
    public const CONEXION_USUARIO = 1;
    public const CONEXION_ADMIN = 2;    

    public function __construct() {
        $conexion = null;
    }

    /**
     * Función que obtenie y retorna la conexión con la base
     * de datos en base el tipo de usuario que requiere.
     * @Parámetros: El tipo de conexion a iniciar (int).
     * @Retorna: Objeto con la conexion (PDO).
     */
    public static function getConexion($tipoConexion) {
        $conexion = null;
        try {
            if ($tipoConexion === self::CONEXION_BASICA) {
                $conexion = new PDO(self::url, 'root', 'admin');
            }
            if ($tipoConexion === self::CONEXION_USUARIO) {
                $conexion = new PDO(self::url, 'root', 'admin');
            }
            if ($tipoConexion === self::CONEXION_ADMIN) {
                $conexion = new PDO(self::url, 'root', 'admin');
            }
            $estatus = true;
        } catch (PDOException $e) {
            throw $e;
            $conexion = null;
        }
        return $conexion;
    }

}

?>