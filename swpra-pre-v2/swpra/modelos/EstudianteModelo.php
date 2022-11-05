<?php

include_once("entidades/Estudiante.php");
include_once("interfaces/InicioSesion.php");
include_once("interfaces/ActualizacionFlexible.php");
require_once('utilidades/ExtractorPropiedades.php');

class EstudianteModelo extends Actualizador implements InicioSesion {

    public const TABLA_USUARIOS = 't_usuarios';
    public const TABLA_ESTUDIANTES = 't_estudiantes';

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
    public const COL_MATRICULA = 'matricula';
    public const COL_CUATRIMESTRE = 'fk_id_cuatrimestre';
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
            throw new Exception("Objeto no válido $ usuario . Se esperaba Estudiante.");
        }
        //$sql = sprintf("SELCT 1 FROM %s WHERE %s = ? AND %s = 'estudiante", self::TABLA_USUARIOS, self::EMAIL, self::ROL);
        $sql = "SELECT 1 FROM t_usuarios WHERE email = ? AND rol = 'estudiante'";
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
            throw new Exception("Objeto no válido $ usuario . Se esperaba Estudiante.");
        }
        $estudiante = null;
        //$sql = sprintf("SELECT %s, %s FROM %s WHERE %s = ? AND %s = md5(?)", self::EMAIL, self::ROL, self::TABLA_USUARIOS, self::EMAIL, self::CONTRASENA);
        $sql = "SELECT email, rol FROM t_usuarios WHERE email = ? AND contrasena = md5(?)";
        $sentencia = $conexion->prepare($sql); // PDOStatement
        $sentencia->bindValue(1, $usuario->getEmail(), PDO::PARAM_STR);
        $sentencia->bindValue(2, $usuario->getContrasena(), PDO::PARAM_STR);
        if ($sentencia->execute() && $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $estudiante = new Estudiante();
            $estudiante->setEmail(($resultado[0])['email']);
            $estudiante->setRol(($resultado[0])['rol']);
        }
        $conexion = null;
        return $estudiante;
    }

    public function insertarUsuario($estudiante, &$conexion) {
        $class = 'Estudiante';
        if ( !isset($estudiante) || !($estudiante instanceof $class) ) {
            throw new Exception("Objeto no válido $ usuario . Se esperaba Estudiante.");
        }
        $sql =
        "INSERT INTO t_usuarios (email, contrasena, rol, nombre, apellido_pat, apellido_mat, fecha_nac, sexo, dir_foto, fk_id_programa_edu)
         VALUES (?, md5(?), 'estudiante', ?, ?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue(1, $estudiante->getEmail(), PDO::PARAM_STR);
            $sentencia->bindValue(2, $estudiante->getContrasena(), PDO::PARAM_STR);
            $sentencia->bindValue(3, $estudiante->getNombre(), PDO::PARAM_STR);
            $sentencia->bindValue(4, $estudiante->getApaterno(), PDO::PARAM_STR);
            $sentencia->bindValue(5, $estudiante->getAmaterno(), PDO::PARAM_STR);
            $sentencia->bindValue(6, $estudiante->getFnacimiento(), PDO::PARAM_STR);
            $sentencia->bindValue(7, $estudiante->getSexo(), PDO::PARAM_STR);
            $sentencia->bindValue(8, $estudiante->getDirFoto(), PDO::PARAM_STR);
            $sentencia->bindValue(9, $estudiante->getIdPrograma(), PDO::PARAM_INT);
        $sentencia->execute();
        if ($sentencia->rowCount() === 0) {
            throw new Exception("Cero filas afectadas al insertar el usuario. Rollback.");
        }
    }

    public function insertarEstudiante($estudiante, &$conexion) {
        $class = 'Estudiante';
        if ( !isset($estudiante) || !($estudiante instanceof $class) ) {
            throw new Exception("Objeto no válido $ usuario. Se esperaba Estudiante.");
        }
        $sql =
        "INSERT INTO t_estudiantes (id, matricula, fk_id_cuatrimestre, fk_email)
         VALUES (0, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue(1, $estudiante->getMatricula(), PDO::PARAM_STR);
            $sentencia->bindValue(2, $estudiante->getIdCuatrimestre(), PDO::PARAM_INT);
            $sentencia->bindValue(3, $estudiante->getEmail(), PDO::PARAM_STR);
        $sentencia->execute();
        if ($sentencia->rowCount() === 0) {
            throw new Exception("Cero filas afectadas al insertar el estudiante. Rollback.");
        }
    }

    public function consultarPorID($email, &$conexion) {

        $estudiante = null;

        $sql  = 'SELECT * FROM t_usuarios INNER JOIN t_estudiantes
        ON t_usuarios.email = t_estudiantes.fk_email WHERE email = ?';

        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(1, $email, PDO::PARAM_STR);

        if ($sentencia->execute() && $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
            $fila = $resultado[0];
            $estudiante = new Estudiante();
            $estudiante->setEmail($fila['email']);
            $estudiante->setNombre($fila['nombre']);
            $estudiante->setApaterno($fila['apellido_pat']);
            $estudiante->setAmaterno($fila['apellido_mat']);
            $estudiante->setRol($fila['rol']);
            $estudiante->setFnacimiento($fila['fecha_nac']);
            $estudiante->setSexo($fila['sexo']);
            $estudiante->setDirFoto($fila['dir_foto']);
            $estudiante->setIdPrograma($fila['fk_id_programa_edu']);
            $estudiante->setId($fila['id']);
            $estudiante->setMatricula($fila['matricula']);
            $estudiante->setIdCuatrimestre($fila['fk_id_cuatrimestre']);
        }

        return $estudiante;

    }

    

}

?>