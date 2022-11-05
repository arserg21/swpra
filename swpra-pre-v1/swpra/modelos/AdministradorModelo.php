<?php

include_once("entidades/Administrador.php");
include_once("interfaces/InicioSesion.php");

class AdministradorModelo implements InicioSesion {

    public function __construct() {
    }

    public function existe($usuario, $conexion) {
        $class = 'Usuario';
        if (!isset($usuario) || !($usuario instanceof $class)) {
            throw new Exception("Objeto no válido: Se esperaba un Administrador.");
        }
        $sql = "SELECT 1 FROM t_administradores WHERE email = ?";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue(1, $usuario->getEmail(), PDO::PARAM_STR);
        $sentencia->execute();
        $existe = array_key_exists(0, $sentencia->fetchAll(PDO::FETCH_ASSOC));
        $conexion = null;
        return $existe;
    }

    public function hacerMatch($usuario, $conexion) {
        $class = 'Usuario';
        if (!isset($usuario) || !($usuario instanceof $class)) {
            throw new Exception("Objeto no válido: Se esperaba un Administrador.");
        }
        $administrador = null;
        $sql = "SELECT email, 'rol' AS rol FROM t_administradores WHERE email = ? AND contrasena = md5(?)";
        $sentencia = $conexion->prepare($sql); // PDOStatement
        $sentencia->bindValue(1, $usuario->getEmail(), PDO::PARAM_STR);
        $sentencia->bindValue(2, $usuario->getContrasena(), PDO::PARAM_STR);
        if ( $sentencia->execute() && $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC) ) {
            $administrador = new Administrador();
            $administrador->setEmail(($resultado[0])['email']);
            $administrador->setRol(($resultado[0])['rol']);
        }
        $conexion = null;
        return $administrador;
    }

}

?>