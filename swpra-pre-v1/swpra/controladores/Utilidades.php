<?php

final class POSTClass {

    public static function buscar($claves = array()) {
        if (!is_array($claves)) {
            return false;
        }
        $existenTodos = true;
        foreach($claves as $clave => $valor) {
            $existenTodos = $existenTodos && array_key_exists($clave, $_POST);
            if (!$existenTodos) {
                break;
            }
        }
        return $existenTodos;
    }

    /*public static function recuperar($claves) {
        $parametros = array();
        if (!is_array($claves)) {
            return $parametros;
        }
        foreach($claves as $clave) {
            if (array_key_exists($clave, $_POST)) {
                $parametros[''.$clave.''] = htmlentities($_POST[''.$clave.'']);
            } else {
                $parametros = array();
                break;
            }
        }
        return $parametros;
    }*/

    public static function recuperar($claves) {
        $parametros = array();
        if (!is_array($claves)) {
            return $parametros;
        }
        foreach($claves as $clave => $valor) {
            if (array_key_exists($clave, $_POST)) {
                $parametros[''.$valor.''] = htmlentities($_POST[''.$clave.'']);
            } else {
                $parametros = array();
                break;
            }
        }
        return $parametros;
    }

}

final class Utilidades {

    public function __construct() {
    }

    public static function generarToken() {
        $semilla = "abcdefghijklmnopqrstuvwxyz0123456789";
        $max = strlen($semilla) - 1;
        $token = "";
        for ($i = 0; $i <= 7; $i++) {
            $token .= $semilla[rand(0, $max)];
        }
        return $token;
    }
    
    public static function existenEn($superGlobal, $claves) {
        if (!is_array($claves) || !is_string($superGlobal)) {
            return false;
        }
        $existenTodos = true;
        foreach($claves as $clave) {
            if (strcasecmp("post", $superGlobal) === 0) {
                $existenTodos = $existenTodos && array_key_exists($clave, $_POST);
            } elseif (strcasecmp("get", $superGlobal) === 0) {
                $existenTodos = $existenTodos && array_key_exists($clave, $_GET);
            } else {
                $existenTodos = false;
                break;
            }
            if (!$existenTodos) {
                break;
            }
        }
        return $existenTodos;
    }

}

final class PaqueteJSON {

    //private $recepcion = false; // Recibio los datos ?
    //private $adecuado  = false; // Datos adecuados ?
    //private $accion    = false; // Entendio la acción ?
    private $ok        = false; // Sin exepciones ó no break?
    private $mensaje   = '';    // Mensaje al usuario (opcional)
    //private $error     = '';    // Mensaje en caso de exepción (opcional)
    private $datos     = null;  // Datos a devolver (opcional)

    public function __construct() {
    }

    public function setOk($ok) {
        $this->ok = $ok;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    public function setDatos($datos) {
        $this->datos = $datos;
    }

    public function obtenerDatos() {
        return array(
            'ok'      => $this->ok,
            'mensaje' => $this->mensaje,
            'datos'   => $this->datos
        );
    }
}

abstract class Validador {

    const CODIGO_1 = 1; // Por error del programador.
    const CODIGO_2 = 2; // Por error del usuario.

    const MENSAJE_CODIGO_1 = 'Parametro de la función no válido: ';
    const MENSAJE_CODIGO_2 = 'Los datos no tienen un formato válido: ';

    const PATERNO = 1;
    const MATERNO = 2;

    public static function validarEmail($email) {
        if (!isset($email) || !is_string($email) || is_null($email)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $email.', self::CODIGO_1);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(self::MENSAJE_CODIGO_2 . 'Dirección de correo electrónico.', self::CODIGO_2);
        }
        return true;
    }

    public static function validarNombre($nombre) {
        if (!isset($nombre) || !is_string($nombre) || is_null($nombre)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $nombre.', self::CODIGO_1);
        }
        if (!(preg_match('/^(?=.{3,18}$)[a-zñA-ZÑ](\s?[a-zñA-ZÑ])*$/', $nombre) === 1)) {
            throw new Exception(self::MENSAJE_CODIGO_2 . 'Nombre.', self::CODIGO_2);
        }
        return true;
    }

    public static function validarApellido($tipo, $apellido) {
        if (!isset($apellido) || !is_string($apellido) || is_null($apellido)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $apellido.', self::CODIGO_1);
        }
        if (!isset($tipo) || !is_int($tipo) || is_null($tipo) || !($tipo === self::PATERNO || $tipo === self::MATERNO)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $tipo.', self::CODIGO_1);
        }
        if ($tipo === self::PATERNO) {
            if (!(preg_match('/^(?=.{3,18}$)[a-zñA-ZÑ](\s?[a-zñA-ZÑ])*$/', $apellido) === 1)) {
                throw new Exception(self::MENSAJE_CODIGO_2 . 'Apellido paterno.', self::CODIGO_2);
            }
        }
        if ($tipo === self::MATERNO) {
            if (!((preg_match('/^(?=.{3,18}$)[áéíóúÁÉÍÓÚa-zA-Z](\s?[áéíóúÁÉÍÓÚa-zA-Z áéíóúÁÉÍÓÚ])*$/', $apellido) === 1) || (strlen($apellido) === 0))) {
                throw new Exception(self::MENSAJE_CODIGO_2 . 'Apellido materno.', self::CODIGO_2);
            }
        }
        //'/^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$/g'
        return true;
    }

    public static function validarSexo($sexo) {
        if (!isset($sexo) || !is_string($sexo) || is_null($sexo)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $sexo.', self::CODIGO_1);
        }
        if (!($sexo == 'hombre' || $sexo == 'mujer')) {
            throw new Exception(self::MENSAJE_CODIGO_2 . ' Sexo.', self::CODIGO_2);
        }
        return true;
    }

    public static function validarContrasena($contrasena1, $contrasena2) {
        if (!isset($contrasena1) || !is_string($contrasena1) || is_null($contrasena1)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $contrasena1.', self::CODIGO_1);
        }
        if (!isset($contrasena2) || !is_string($contrasena2) || is_null($contrasena2)) {
            throw new Exception(self::MENSAJE_CODIGO_1 . 'Var $contrasena2.', self::CODIGO_1);
        }
        if (!(preg_match('/^(?=.{8,50}$)[a-zA-Z0-9](\s?[a-zA-Z0-9])*$/', $contrasena1) === 1)) {
            throw new Exception('Los datos no tienen un formato válido. (contraseña no válida)');
        }
        if (!($contrasena1 === $contrasena2)) {
            throw new Exception('Los datos no tienen un formato válido. (contraseñas no coinciden)');
        }
        return true;
    }

}

?>