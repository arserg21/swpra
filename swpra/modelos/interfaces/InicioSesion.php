<?php

interface InicioSesion {

    /**
     * Método que determina si un usuario esta dado de
     * alta en la base de datos.
     * @Parametros: Objeto estudiante, profesor ó administrador,
     *              Objeto conexion (PDO).
     * @Retorna: bool.
     */
    public function existe($usuario, $conexion);

    /**
     * Método que recupera el correo electrónico y el rol
     * de un usuario dedado de alta en la base de datos.
     * @Parametros: Objeto estudiante, profesor ó administrador,
     *              Objeto conexion (PDO).
     * @Retorna: Objeto estudiante, profesor ó administrador.
     */
    public function hacerMatch($usuario, $conexion);

}

?>