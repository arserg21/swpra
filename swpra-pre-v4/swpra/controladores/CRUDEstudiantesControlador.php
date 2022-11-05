<?php

require_once('Utilidades.php');
require_once('../modelos/ConexionModelo.php');
require_once('../modelos/EstudianteModelo.php');
require_once('../modelos/ProgramaModelo.php');
require_once('../modelos/CuatrimestreModelo.php');
require_once('../modelos/entidades/Estudiante.php');
require_once('../modelos/entidades/Programa.php');
require_once('../modelos/entidades/Cuatrimestre.php');
require_once('../modelos/entidades/Token.php');
require_once('../modelos/TokenModelo.php');
require_once('EnviarEmailControlador.php');

$accionesPermitidas = array(
    'consultarTodo', // Consulta y devuelve todos los registros de una tabla
    'ver',           // Consulta y devuelve un registro de una tabla
    'modificar',     // Modificar un registro de una tabla
    'eliminar',      // Eliminar un registro de una tabla
    'buscar',        // Busca y devuelve un registro de una tabla
    'crear',         // Insertar un registro en una tabla
    'obtenerExtras'  // Obtienes los cuatrimestres y los programas
);

$pjson = new PaqueteJSON();

/*$_POST['accion'] = 'modificar';
$_POST['email'] = 'sergio@mail.com';*/

try {

    // Se verifica que exista una acción:
    array_key_exists('accion', $_POST) or
        throw new Exception('Parametros insuficientes.');
        $accion = $_POST['accion'];

    // Se verifica que la acción sea permitida:
    in_array($accion, $accionesPermitidas) or
        throw new Exception('Acción no reconocida.');
    
    switch($accion) {

        case 'consultarTodo':

            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_ADMIN);
            $modEstudiante = new EstudianteModelo();

            // Extrae los registros con rol = 'estudiante' de la tabla t_usuarios:
            $registros = $modEstudiante->consultarTablaCRUD($bd);
            $bd = null;

            $pjson->setDatos(array('usuarios' => $registros));
            $pjson->setOk(true);

            break;
        
        case 'ver':

            $parametrosRequeridos = array('email');

            array_key_exists('email', $_POST) or
                throw new Exception('Parametros insuficientes.');
                $parametros = POSTClass::recuperar($parametrosRequeridos);
                $email = $parametros['email'];

            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_ADMIN);
            
            $modEstudiante   = new EstudianteModelo();
            $modPrograma     = new ProgramaModelo();
            $modCuatrimestre = new CuatrimestreModelo();

            // Extrae el registro con el email pasado por post
            // de la tabla t_usuarios:
            $usuario = $modEstudiante->consultarPorID($email, $bd);

            // Extrae los registros complementarios:
            $programa     = null;
            $cuatrimestre = null;

            is_null($usuario->getIdPrograma()) or 
                $programa = $modPrograma->consultarPorID($usuario->getIdPrograma(), $bd);

            is_null($usuario->getIdCuatrimestre()) or
                $cuatrimestre = $modCuatrimestre->consultarPorID($usuario->getIdCuatrimestre(), $bd);
            
            
            // Extrae los registros extra:
            $programas     = $modPrograma->consultarTodo($bd);
            $cuatrimestres = $modCuatrimestre->consultarTodo($bd);

            $bd = null;

            $pjson->setDatos(
                array(
                    'usuario' => $usuario,
                    'programaReal' => $programa,
                    'cuatrimestreReal' => $cuatrimestre,
                    'programas' => $programas,
                    'cuatrimestres' => $cuatrimestres
                )
            );
            $pjson->setOk(true);

            break;
        
        case 'modificar':

            $parametrosRequeridos = array('form');

            array_key_exists('form', $_POST) or
                throw new Exception('Parametros insuficientes');
                $parametros = POSTClass::recuperar($parametrosRequeridos);
                $fomulario = $parametros['form'];
            
            $formularios = array(
                'formDatosPersonalesEstudiante',
                'formDatosEscolaresEstudiante',
                'formContrasenaEstudiante'
            );
            
            in_array($fomulario, $formularios) or
                throw new Exception('Acción no reconocida');
            
            $camposFormDatosPersonales = array(
                'email',
                'fnacimientoEstudianteModificar',
                'maternoEstudianteModificar',
                'nombreEstudianteModificar',
                'paternoEstudianteModificar',
                'sexo'
            );
            $camposFormDatosEscolares  = array(
                'email',
                'cuatrimestreEstudianteModificar',
                'matriculaEstudianteModificar',
                'programaEstudianteModificar'
            );
            $camposFormContrasena      = array(
                'email',
                'contrasenaNuevaEstudiante1',
                'contrasenaNuevaEstudiante2'
            );
            
            switch($fomulario) {

                case 'formDatosPersonalesEstudiante':

                    // Verificar si todos los parametros fueron recibidos:
                    POSTClass::buscar($camposFormDatosPersonales) or
                        throw new Exception('Parámetros insuficientes');
                        $campos = POSTClass::recuperar($camposFormDatosPersonales);
                    
                    // Validar los datos:
                    Validador::validarEmail($campos['email']);
                    Validador::validarNombre($campos['nombreEstudianteModificar']);
                    Validador::validarApellido(Validador::PATERNO, $campos['paternoEstudianteModificar']);
                    Validador::validarApellido(Validador::MATERNO, $campos['maternoEstudianteModificar']);
                    Validador::validarSexo($campos['sexo']);
                    
                    // Encapsular datos en objeto:
                    $estudiante = new Estudiante();
                    $estudiante->setEmail($campos['email']);
                    $estudiante->setNombre($campos['nombreEstudianteModificar']);
                    $estudiante->setApaterno($campos['paternoEstudianteModificar']);
                    $estudiante->setAmaterno($campos['maternoEstudianteModificar'] === '' ? null : $campos['maternoEstudianteModificar']);
                    $estudiante->setFnacimiento($campos['fnacimientoEstudianteModificar']);
                    $estudiante->setSexo($campos['sexo']);

                    unset($campos);

                    // Crear modelos necesarios:
                    $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                    $modEstudiante = new EstudianteModelo();

                    // Crear sentencia de actualización:
                    $modEstudiante->agregarActualizacion(EstudianteModelo::COL_NOMBRE,  $estudiante->getNombre(),      PDO::PARAM_STR);
                    $modEstudiante->agregarActualizacion(EstudianteModelo::COL_PATERNO, $estudiante->getApaterno(),    PDO::PARAM_STR);
                    $modEstudiante->agregarActualizacion(EstudianteModelo::COL_MATERNO, $estudiante->getAmaterno(),    PDO::PARAM_STR);
                    $modEstudiante->agregarActualizacion(EstudianteModelo::COL_FE_NAC,  $estudiante->getFnacimiento(), PDO::PARAM_STR);
                    $modEstudiante->agregarActualizacion(EstudianteModelo::COL_SEXO,    $estudiante->getSexo(),        PDO::PARAM_STR);
                    
                    $modEstudiante->setClausulaWhere(EstudianteModelo::COL_EMAIL, $estudiante->getEmail(), PDO::PARAM_STR);

                    $modEstudiante->construirSql(EstudianteModelo::TABLA_USUARIOS);

                    $modEstudiante->actualizar($bd);

                    $bd = null;

                    $pjson->setMensaje($modEstudiante->getReporteActualizacion());

                    break;
                case 'formDatosEscolaresEstudiante':

                    // Verificar si todos los parametros fueron recibidos:
                    POSTClass::buscar($camposFormDatosEscolares) or
                        throw new Exception('Parámetros insuficientes');
                        $campos = POSTClass::recuperar($camposFormDatosEscolares);

                    // Válidar los datos:
                    $campos['programaEstudianteModificar'] = (int)$campos['programaEstudianteModificar'];
                    $campos['cuatrimestreEstudianteModificar'] = (int)$campos['cuatrimestreEstudianteModificar'];

                    if ($campos['programaEstudianteModificar'] === 0) {
                        $campos['programaEstudianteModificar'] = null;
                    }

                    if ($campos['cuatrimestreEstudianteModificar'] === 0) {
                        $campos['cuatrimestreEstudianteModificar'] = null;
                    }
                    
                    // Encapsular datos en objeto:
                    $estudiante = new Estudiante();
                    $estudiante->setEmail($campos['email']);
                    $estudiante->setMatricula($campos['matriculaEstudianteModificar'] === '' ? null : $campos['matriculaEstudianteModificar']);
                    $estudiante->setIdPrograma($campos['programaEstudianteModificar']);
                    $estudiante->setIdCuatrimestre($campos['cuatrimestreEstudianteModificar']);

                    unset($campos);

                    // Crear modelos necesarios:
                    $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                    $modEstudiante1 = new EstudianteModelo(); // Actualizara el programa: t_usuarios
                    $modEstudiante2 = new EstudianteModelo(); // Actualizara la matricula y el cuatrimestre: t_estudiantes

                    // Crear sentencia de actualización:
                    $modEstudiante1->agregarActualizacion(EstudianteModelo::COL_PROG_EDU,     $estudiante->getIdPrograma(),        PDO::PARAM_INT);
                    $modEstudiante2->agregarActualizacion(EstudianteModelo::COL_MATRICULA,    $estudiante->getMatricula(),         PDO::PARAM_STR);
                    $modEstudiante2->agregarActualizacion(EstudianteModelo::COL_CUATRIMESTRE, $estudiante->getIdCuatrimestre(),    PDO::PARAM_INT);
                    
                    $modEstudiante1->setClausulaWhere(EstudianteModelo::COL_EMAIL,    $estudiante->getEmail(), PDO::PARAM_STR);
                    $modEstudiante2->setClausulaWhere(EstudianteModelo::COL_EMAIL_FK, $estudiante->getEmail(), PDO::PARAM_STR);

                    $modEstudiante1->construirSql(EstudianteModelo::TABLA_USUARIOS);
                    $modEstudiante2->construirSql(EstudianteModelo::TABLA_ESTUDIANTES);

                    try {

                        $bd->beginTransaction();
                            $modEstudiante1->actualizar($bd);
                            $modEstudiante2->actualizar($bd);
                        $bd->commit();

                    } catch (Exception $ie) {

                        $bd->rollback();
                        $bd = null;
                        throw new Exception('No es fue posible actualizar sus datos de manera segura.');

                    }

                    $bd = null;

                    $pjson->setMensaje($modEstudiante1->getReporteActualizacion() . '<br/>' . $modEstudiante2->getReporteActualizacion());

                    break;
                case 'formContrasenaEstudiante':

                    // Búscar los campos necesarios:
                    POSTClass::buscar($camposFormContrasena) or
                        throw new Exception('Parámetros insuficientes.');
                        $campos = POSTClass::recuperar($camposFormContrasena);
                        
                        // Válidar los datos:
                        Validador::validarContrasena(
                            $campos['contrasenaNuevaEstudiante1'],
                            $campos['contrasenaNuevaEstudiante2']
                        );

                        // Crear modelos necesarios:
                        $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                        $modEstudiante = new EstudianteModelo();

                        

                        // Comprobacion:
                        /*$contrasenaComprobada = $modEstudiante->comprobarContrasena(
                            $estudiante->getEmail(),
                            $estudiante->getContrasena(),
                            $bd
                        );*/

                        //$contrasenaComprobada or throw new Exception('Su contraseña actual no es correcta.');

                        //$campos['contrasenaActual'] = md5($campos['contrasenaActual']);
                        $campos['contrasenaNuevaEstudiante1'] = md5($campos['contrasenaNuevaEstudiante1']);
                        $campos['contrasenaNuevaEstudiante2'] = md5($campos['contrasenaNuevaEstudiante2']);

                        // Encapsular datos en objeto:
                        $estudiante = new Estudiante();
                        $estudiante->setEmail($campos['email']);
                        $estudiante->setContrasena($campos['contrasenaNuevaEstudiante1']);

                        // Crear sentencia de actualización:
                        $modEstudiante->agregarActualizacion(EstudianteModelo::COL_CONTRASENA, $estudiante->getContrasena(), PDO::PARAM_STR);
                        
                        $modEstudiante->setClausulaWhere(EstudianteModelo::COL_EMAIL, $estudiante->getEmail(), PDO::PARAM_STR);

                        $modEstudiante->construirSql(EstudianteModelo::TABLA_USUARIOS);

                        
                        $modEstudiante->actualizar($bd);

                        $bd = null;

                        $pjson->setMensaje($modEstudiante->getReporteActualizacion());

                        unset($campos);

                    break;

            }

            $pjson->setOk(true);

            break;
        case 'eliminar':

            $parametrosRequeridos = array('email');

            array_key_exists('email', $_POST) or
                throw new Exception('Parametros insuficientes.');
                $parametros = POSTClass::recuperar($parametrosRequeridos);
                $email = $parametros['email'];

            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_ADMIN);
            
            $modEstudiante   = new EstudianteModelo();

            $eliminado = $modEstudiante->eliminarPorID($email, $bd);

            if ($eliminado) {
                $pjson->setMensaje('Registro eliminado');
                $pjson->setDatos(array('registroEliminado' => true));
            } else {
                $pjson->setMensaje('Registro no eliminado');
                $pjson->setDatos(array('registroEliminado' => false));
            }

            $bd = null;
            
            //$pjson->setMensaje('Voy a eliminar algo.');
            //$pjson->setDatos($_POST);
            $pjson->setOk(true);

            break;
        
        case 'buscar':

            $parametrosRequeridos = array('email');
            POSTClass::buscar($parametrosRequeridos) or
                throw new Exception('Parámetros insuficientes');
                $campos = POSTClass::recuperar($parametrosRequeridos);
            
            /*$campos['email'] === $campos['emailEstudiante'] or
                throw new Exception('Acción no segura: Los datos no coinciden');*/
            
            Validador::validarEmail($campos['email']);
            
            //
            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
            $modEstudiante = new EstudianteModelo();

            $usuario = $modEstudiante->consultarPorID($campos['email'], $bd);

            $bd = null;

            //$pjson->setMensaje('voy a buscar algo');
            $pjson->setDatos(array('usuario' => $usuario));
            $pjson->setOk(true);

            break;
        
        case 'crear':

            // Recuperar los campos:
            $parametrosRequeridos = array(
                'emailEstudianteCrear',
                'nombreEstudianteCrear',
                'paternoEstudianteCrear',
                'maternoEstudianteCrear',
                'fnacimientoEstudianteCrear',
                'sexo',
                'matriculaEstudianteCrear',
                'cuatrimestreEstudianteCrear',
                'programaEstudianteCrear',
                'contrasenaEstudianteCrear1',
                'contrasenaEstudianteCrear2'
            );

            POSTClass::buscar($parametrosRequeridos) or
                throw new Exception('Parámetros insuficientes');
                $campos = POSTClass::recuperar($parametrosRequeridos);

            // Válidar los datos:
            Validador::validarEmail($campos['emailEstudianteCrear']);
            Validador::validarNombre($campos['nombreEstudianteCrear']);
            Validador::validarApellido(Validador::PATERNO, $campos['paternoEstudianteCrear']);
            Validador::validarApellido(Validador::MATERNO, $campos['maternoEstudianteCrear']);
            Validador::validarSexo($campos['sexo']);
            Validador::validarContrasena($campos['contrasenaEstudianteCrear1'], $campos['contrasenaEstudianteCrear2']);

            // Válidar los datos:
            $campos['programaEstudianteCrear'] = (int)$campos['programaEstudianteCrear'];
            $campos['cuatrimestreEstudianteCrear'] = (int)$campos['cuatrimestreEstudianteCrear'];

            if ($campos['programaEstudianteCrear'] === 0) {
                $campos['programaEstudianteCrear'] = null;
            }

            if ($campos['cuatrimestreEstudianteCrear'] === 0) {
                $campos['cuatrimestreEstudianteCrear'] = null;
            }

            // Creación de los objetos:
            $preEstudiante = new Estudiante();
            $preEstudiante->setEmail($campos['emailEstudianteCrear']);
            $preEstudiante->setContrasena($campos['contrasenaEstudianteCrear1']);
            $preEstudiante->setNombre($campos['nombreEstudianteCrear']);
            $preEstudiante->setApaterno($campos['paternoEstudianteCrear']);
            $preEstudiante->setAmaterno($campos['maternoEstudianteCrear']);
            $preEstudiante->setFnacimiento($campos['fnacimientoEstudianteCrear']);
            $preEstudiante->setSexo($campos['sexo']);
            $preEstudiante->setMatricula($campos['matriculaEstudianteCrear'] === '' ? null : $campos['matriculaEstudianteCrear']);
            $preEstudiante->setIdCuatrimestre($campos['cuatrimestreEstudianteCrear']);
            $preEstudiante->setIdPrograma($campos['programaEstudianteCrear']);

            $conexion = ConexionModelo::getConexion(ConexionModelo::CONEXION_ADMIN);
            $modEstudiante = new EstudianteModelo();

            // Comprobaciones:
            if ($modEstudiante->existe($preEstudiante, $conexion)) {
                throw new Exception('La dirección de correo electrónico ya esta registrada.');
            }

            //throw new Exception('aqui no');

            // Creación de objetos:
            $modToken = new TokenModelo();
            $token = new Token(
                0, Utilidades::generarToken(), null,
                "verificacion", "no", $preEstudiante->getEmail()
            );

            

            //throw new Exception('todo bien, pero no te voy a registrar');

            // Inicio de la transacción:

            $banderaTransaccion = true;

            $conexion->beginTransaction();
                $modEstudiante->insertarUsuario($preEstudiante, $conexion);
                $modEstudiante->insertarEstudiante($preEstudiante, $conexion);
                $modToken->insertar($token, $conexion);
            $conexion->commit();

            $pjson->setDatos(array('registrado' => true));

            /*SWPRAEmailer::enviar(
                $campos['emailEstudianteCrear'],
                $campos['nombreEstudianteCrear'],
                $token->getToken()
            );*/

            $pjson->setOk(true);
            $pjson->setMensaje('Registrado');

            //session_start();
            //$_SESSION['usuario'] = $campos['correoElectronico'];

            break;
        
        case 'obtenerExtras':

            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_ADMIN);

            $modPrograma     = new ProgramaModelo();
            $modCuatrimestre = new CuatrimestreModelo();

            // Extrae los registros complementarios:
            $programa     = null;
            $cuatrimestre = null;
            
            // Extrae los registros extra:
            $programas     = $modPrograma->consultarTodo($bd);
            $cuatrimestres = $modCuatrimestre->consultarTodo($bd);

            $bd = null;

            $pjson->setDatos(
                array(
                    'programas' => $programas,
                    'cuatrimestres' => $cuatrimestres
                )
            );
            $pjson->setOk(true);

            break;
    }


} catch (Exception $e) {
    
    $pjson->setOk(false);
    $pjson->setMensaje($e->getMessage());
    $pjson->setDatos(null);

} finally {

    print_r(json_encode($pjson));

}

?>