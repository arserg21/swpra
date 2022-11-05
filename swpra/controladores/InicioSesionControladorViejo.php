<?php

/**
 * Se incluyen los archivos con las clases necesarias para el controlador.
 */
include_once("./../modelos/ConexionModelo.php");
include_once("./../modelos/EstudianteModelo.php");
include_once("./../modelos/ProfesorModelo.php");
include_once("./../modelos/AdministradorModelo.php");
include_once("./../modelos/entidades/Estudiante.php");
include_once("./../modelos/entidades/Profesor.php");
include_once("./../modelos/entidades/Administrador.php");
include_once("ControladorGenerico.php");

class InicioSesionControlador extends ControladorGenerico {

    private $estudianteModelo;
    private $profesorModelo;
    private $administradorModelo;

    private $usuario;
    private $autenticado = false;

    public function __construct($estudianteModelo, $profesorModelo, $administradorModelo) {
        parent::__construct();
        // Añadir llaves extra a ocupar:
        $this->datosJSON['autenticado'] = false;
        // Asignación de modelos:
        $this->estudianteModelo = $estudianteModelo;
        $this->profesorModelo = $profesorModelo;
        $this->administradorModelo = $administradorModelo;
    }

    /**
     * Método principal que ejecuta el controlador.
     */
    public function iniciar() {

        if (!self::existenEn("post", array("accion"))) {
            // Nada por hacer.
            return;
        }

        // Sección de las acciones (2) ...

        if ($_POST['accion'] === "verificar") {
            self::verificarSesion();
            $this->cumplioTarea = true;
            $this->mensaje = $this->sesionActiva ?
                "Hay sesión activa." :
                "No hay sesión activa.";
            return;
        }

        if ($_POST['accion'] === "autenticar") {

            self::recuperarDatos();

            if (!$this->datosRecuperados) {
                // Nada por hacer.
                return;
            }

            $bandera = true;
            $usuario = new Estudiante();
            $usuario->setEmail($this->datos['correoElectronico']);
            $usuario->setContrasena($this->datos['contrasena']);

            try {

                $conexion = ConexionModelo::getConexion(ConexionModelo::CONEXION_BASICA);

                $modelos = array(
                    &$this->estudianteModelo,
                    &$this->profesorModelo,
                    &$this->administradorModelo
                );

                foreach ($modelos as $modelo) {
                    $resultado = self::autenticar($modelo, $usuario, $conexion);
                    if ($resultado === 0) {
                        // Correo no encontrado:
                        $bandera = false;
                        continue;
                    } elseif ($resultado === 1) {
                        // Autenticado:
                        $bandera == true;
                        break;
                    } elseif ($resultado === 2) {
                        // Contraseña incorrecta:
                        $bandera = false;
                        break;
                    }
                }

                $conexion = null;
                $this->cumplioTarea = true;

            } catch (Exception $ex) {
                $bandera = false;
                $this->ok = false;
                $this->mensaje = "Ha sucedido algo inesperado, por favor intentalo más tarde.";
                //echo $ex->getMessage();
            }

            if ($bandera) {

                // Se ha autenticado correctamente...
                session_start();
                $_SESSION['usuario'] = $this->usuario->getEmail();
                $this->autenticado = true;
                $this->sesionActiva = true;
                $this->mensaje = "¡Autentificación exitosa! Redireccionando...";

            }

        }

    }

    /**
     * Método que se encarga de recuperar los datos recibidos atráves del
     * las variables globales $_POST y $_GET.
     */
    protected function recuperarDatos() {
        if (self::existenEn("post", array("correoElectronico", "contrasena"))) {
            // Válidar datos:
            $correoElectronico = htmlentities($_POST['correoElectronico']);
            $contrasena = htmlentities($_POST['contrasena']);
            if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL) || strlen($contrasena) === 0) {
                $this->datosRecuperados = false;
                return;
            }
            // Recuperar datos:
            $this->datos['correoElectronico'] = $correoElectronico;
            $this->datos['contrasena'] = md5($contrasena);
            $this->datosRecuperados = true;
        } else {
            $this->datosRecuperados = false;
        }
    }

    /**
     * Método que se encarga de autenticar al usuario mediante los modelos
     * de Estudiante, Profesor y Administrador.
     */
    private function autenticar($modelo, $usuario, $conexion) {
        $interfaz = 'InicioSesion';
        if (!($modelo instanceof $interfaz)) {
            throw new Exception ("Modelo no válido.");
        }
        if ($modelo->existe($usuario, $conexion)) {
            $this->usuario = $modelo->hacerMatch($usuario, $conexion);
            $conexion = null;
            if (!isset($this->usuario)) {
                $this->autenticado = false;
                $this->mensaje = "Contraseña incorrecta.";
                return 2;
            }
            return 1;
        } else {
            $conexion = null;
            $this->autenticado = false;
            $this->mensaje = "El correo electrónico ingresado no pertenece a ninguna cuenta.";
            return 0;
        }
    }

    public function enviarJSON() {
        $this->datosJSON['autenticado'] = $this->autenticado;
        parent::enviarJSON();
    }

}

/*$_POST['accion'] = "autenticar";
$_POST['correoElectronico'] = "sergio@mail.com";
$_POST['contrasena'] = "adminn";*/

$controlador = new InicioSesionControlador(
    new EstudianteModelo(),
    new ProfesorModelo(),
    new AdministradorModelo()
);

$controlador->iniciar();
$controlador->enviarJSON();


?>