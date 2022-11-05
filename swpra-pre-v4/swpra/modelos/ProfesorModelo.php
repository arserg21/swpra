<?php

include_once("entidades/Profesor.php");
include_once("interfaces/InicioSesion.php");

include_once("interfaces/ActualizacionFlexible.php");

class ProfesorModelo extends Actualizador implements InicioSesion {

    public const TABLA_USUARIOS = 't_usuarios';
    public const TABLA_PROFESORES = 't_profesores';

    public const COL_EMAIL = 'email';
    public const COL_CONTRASENA = 'contrasena';
    public const COL_ROL = 'rol';
    public const COL_NOMBRE = 'nombre';
    public const COL_PATERNO = 'apellido_pat';
    public const COL_MATERNO = 'apellido_mat';
    public const COL_FE_NAC = 'fecha_nac';
    public const COL_SEXO = 'sexo';
    public const COL_DIR_FOTO = 'dir_foto';
    public const COL_PROG_EDU = 'fk_id_programa_edu';

    public const COL_ID = 'id';
    public const COL_CEDULA = 'cedula';
    public const COL_EMAIL_FK = 'fk_email';

    public function __construct() {
    }

    public function comprobarContrasena($email, $contrasena, &$bd) {

        $sql = 'SELECT 1 FROM t_usuarios WHERE email = ? AND contrasena = md5(?)';
        $sentencia = $bd->prepare($sql);
        $sentencia->bindParam(1, $email,      PDO::PARAM_STR);
        $sentencia->bindParam(2, $contrasena, PDO::PARAM_STR);
        if ($sentencia->execute()) {
            return array_key_exists(0, $sentencia->fetchAll(PDO::FETCH_ASSOC));
        }
        throw new Exception('La operación no se puede realizar en este momento.');
        
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

    public function consultarPorID($email, &$conexion) {

        $profesor = null;

        $sql  = 'SELECT * FROM t_usuarios INNER JOIN t_profesores
        ON t_usuarios.email = t_profesores.fk_email WHERE email = ?';

        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(1, $email, PDO::PARAM_STR);

        if ($sentencia->execute() && $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $fila = $resultado[0];
            $profesor = new Profesor();
            $profesor->setEmail($fila['email']);
            $profesor->setNombre($fila['nombre']);
            $profesor->setApaterno($fila['apellido_pat']);
            $profesor->setAmaterno($fila['apellido_mat']);
            $profesor->setRol($fila['rol']);
            $profesor->setFnacimiento($fila['fecha_nac']);
            $profesor->setSexo($fila['sexo']);
            $profesor->setDirFoto($fila['dir_foto']);
            $profesor->setIdPrograma($fila['fk_id_programa_edu']);
            $profesor->setId($fila['id']);
            $profesor->setCedula($fila['cedula']);
        }

        return $profesor;

    }

}

?>