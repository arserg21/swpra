<?php

abstract class ControladorGenerico {

    protected $datos;
    protected $datosJSON;

    // Indica sí el método iniciar() terminó sin excepciones.
    protected $ok = true;
    // Indica sí se ejecuto completamente la acción.
    protected $cumplioTarea = false;
    // Indica sí hay o no una sesión activa.
    protected $sesionActiva = false;
    // Mensaje al usuario.
    protected $mensaje = null;

    protected $datosRecuperados = false;

    public function __construct() {
        $this->datos = array();
        $this->datosJSON = array(
            "ok"=>true,
            "cumplioTarea"=>false,
            "sesionActiva"=>false,
            "mensaje"=>null,
        );
    }

    public abstract function iniciar();
    protected abstract function recuperarDatos();

    /**
     * Método que verifica sí se encuentra una sesión activa.
     * @Parámetros: Sin parámetros.
     * @Retorna: void.
     */
    protected final function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->sesionActiva = isset($_SESSION['usuario']);
    }

    /**
     * Método que verifica si existen los identificadores pasados
     * por parametro en las superglobales $_POST o $_GET.
     * @Parámetros:
     *     - $superGlobal: El nombre de la superglobal (string).
     *     - $identificadores: Llave a encontrar (array).
     * @Retorna: True si todos las claves existen en la superglobal,
     *           false de lo contrario o por parámetros incorrectos. (PDO).
     */
    protected final function existenEn($superGlobal, $claves) {
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

    /**
     * Método que enviar el contenido del array $datosJSON
     * en formato JSON para ser recibido por la vista.
     * @Parámetros: Sin parámetros.
     * @Retorna: void.
     * 
     * Nota: Este método debe de ser sobreescrito por la clase hijo.
     */
    public function enviarJSON() {
        $this->datosJSON['ok'] = $this->ok;
        $this->datosJSON['cumplioTarea'] = $this->cumplioTarea;
        $this->datosJSON['sesionActiva'] = $this->sesionActiva;
        $this->datosJSON['mensaje'] = $this->mensaje;
        print_r(json_encode($this->datosJSON));
    }

}

?>