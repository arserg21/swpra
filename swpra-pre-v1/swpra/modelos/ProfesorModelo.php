<?php

include_once("entidades/Profesor.php");
include_once("interfaces/InicioSesion.php");

class ProfesorModelo implements InicioSesion {

    public function __construct() {
    }

    public function existe($usuario, $conexion)  {
        $class = 'Usuario';
        if ( !isset($usuario) || !($usuario instanceof $class) ) {
            throw new Exception("Objeto no válido $ usuario . Se esperaba Profesor.");
        }
        $sql = "SELECT 1 FROM t_usuarios WHERE email = ? AND rol = 'profesor'";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindValue(1, $usuario->getEmail(), PDO::PARAM_STR);
        $sentencia->execute();
        $existe = array_key_exists(0, $sentencia->fetchAll(PDO::FETCH_ASSOC));
        $conexion = null;
        return $existe;
    }

    public function hacerMatch($usuario, $conexion) {
        $class = 'Usuario';
        if ( !isset($usuario) || !($usuario instanceof $class) ) {
            throw new Exception("Objeto no válido $ usuario . Se esperaba Profesor.");
        }
        $profesor = null;
        $sql = "SELECT email, rol FROM t_usuarios WHERE email = ? AND contrasena = md5(?)";
        $sentencia = $conexion->prepare($sql); // PDOStatement
        $sentencia->bindValue(1, $usuario->getEmail(), PDO::PARAM_STR);
        $sentencia->bindValue(2, $usuario->getContrasena(), PDO::PARAM_STR);
        if ($sentencia->execute() && $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $profesor = new Profesor();
            $profesor->setEmail(($resultado[0])['email']);
            $profesor->setRol(($resultado[0])['rol']);
        }
        $conexion = null;
        return $profesor;
    }

}

?>