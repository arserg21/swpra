<?php

include_once("entidades/Estudiante.php");
include_once("interfaces/InicioSesion.php");

class EstudianteModelo implements InicioSesion {

    public function __construct() {
    }

    public function existe($usuario, $conexion)  {
        $class = 'Usuario';
        if ( !isset($usuario) || !($usuario instanceof $class) ) {
            throw new Exception("Objeto no válido $ usuario . Se esperaba Estudiante.");
        }
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
            $sentencia->bindValue(4, $estudiante->getApellidoPaterno(), PDO::PARAM_STR);
            $sentencia->bindValue(5, $estudiante->getApellidoMaterno(), PDO::PARAM_STR);
            $sentencia->bindValue(6, $estudiante->getFechaNacimiento(), PDO::PARAM_STR);
            $sentencia->bindValue(7, $estudiante->getSexo(), PDO::PARAM_STR);
            $sentencia->bindValue(8, $estudiante->getDirFoto(), PDO::PARAM_STR);
            $sentencia->bindValue(9, $estudiante->getPrograma(), PDO::PARAM_INT);
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

    /*public function insertar($estudiante, $token, $conexion) {
        $class = 'Estudiante';
        if ( !isset($estudiante) || !($estudiante instanceof $class) ) {
            throw new Exception("Objeto no válido $ usuario . Se esperaba Estudiante.");
        }
        $sql1 =
        "INSERT INTO t_usuarios (email, contrasena, rol, nombre, apellido_pat, apellido_mat, fecha_nac, sexo, dir_foto, fk_id_programa_edu)
         VALUES (?, ?, 'estudiante', ?, ?, ?, ?, ?, ?, ?)";
        $sql2 =
        "INSERT INTO t_estudiantes (id, matricula, fk_id_cuatrimestre, fk_email)
         VALUES (0, ?, ?, ?)";
        $inicioLaTransaccion = false;
        $sql3 =
        "INSERT INTO reg_tokens (id, token, fecha_expiracion, tipo, usado, email)
         VALUES (0, ?, adddate(now(), INTERVAL 1 DAY), 'verificacion', 'no', ?)";
        try {
            $sentencia1 = $conexion->prepare($sql1);
                $sentencia1->bindValue(1, $estudiante->getEmail(), PDO::PARAM_STR);
                $sentencia1->bindValue(2, $estudiante->getContrasena(), PDO::PARAM_STR);
                $sentencia1->bindValue(3, $estudiante->getNombre(), PDO::PARAM_STR);
                $sentencia1->bindValue(4, $estudiante->getApellidoPaterno(), PDO::PARAM_STR);
                $sentencia1->bindValue(5, $estudiante->getApellidoMaterno(), PDO::PARAM_STR);
                $sentencia1->bindValue(6, $estudiante->getFechaNacimiento(), PDO::PARAM_STR);
                $sentencia1->bindValue(7, $estudiante->getSexo(), PDO::PARAM_STR);
                $sentencia1->bindValue(8, $estudiante->getDirFoto(), PDO::PARAM_STR);
                $sentencia1->bindValue(9, $estudiante->getPrograma(), PDO::PARAM_INT);
            $sentencia2 = $conexion->prepare($sql2);
                $sentencia2->bindValue(1, $estudiante->getMatricula(), PDO::PARAM_STR);
                $sentencia2->bindValue(2, $estudiante->getIdCuatrimestre(), PDO::PARAM_INT);
                $sentencia2->bindValue(3, $estudiante->getEmail(), PDO::PARAM_STR);
            $sentencia3 = $conexion->prepare($sql3);
                $sentencia3->bindParam(1, $token, PDO::PARAM_STR);
                $sentencia3->bindValue(2, $estudiante->getEmail(), PDO::PARAM_STR);
            // Inicio de la transacción:
            $conexion->beginTransaction();
                $inicioLaTransaccion = true;
                echo "Transacción iniciada<br>";
                $sentencia1->execute();
                echo "Registro insertado en la tabla Usuarios<br>";
                $sentencia2->execute();
                echo "Registro insertado en la tabla Estudiantes<br>";
                $sentencia3->execute();
                echo "Registro insertado en la tabla Tokens<br>";
            $conexion->commit();
            echo "Transacción consignada";
            //$conexion->rollback();
            //echo "Rollback ya que es una prueba<br>";
        } catch (PDOException $e) {
            if ($inicioLaTransaccion) {
                $conexion->rollback();
                echo "Rollback por 'exception'<br>";
            }
            throw $e;
        } finally {
            $conexion = null;
        }
    }*/

    

}

?>