<?php

require_once('../modelos/EstudianteModelo.php');
require_once('../modelos/ProfesorModelo.php');
require_once('../modelos/CuatrimestreModelo.php');
require_once('../modelos/ProgramaModelo.php');
require_once('../modelos/ConexionModelo.php');
require_once('../modelos/entidades/Estudiante.php');
require_once('../modelos/entidades/Profesor.php');
require_once('Utilidades.php');

session_start();

/*$_SESSION['usuario'] = 'sergio@mail.com';
$_SESSION['rol'] = 'estudiante';
$_POST['accion'] = 'actualizar';*/

/*$_POST['form'] = 'form1';
$_POST['nombre'] = 'nosergio';
$_POST['paterno'] = 'noayala';
$_POST['materno'] = 'noromero1';
$_POST['fnacimiento'] = '2000-10-10';
$_POST['sexo'] = 'hombre';*/

/*$_POST['form'] = 'form2';
$_POST['matricula'] = 'nosergio';
$_POST['programa'] = '1002'; // original: 1002
$_POST['cuatrimestre'] = '1008'; // original: 1008*/

/*$_POST['form'] = 'form3';
$_POST['contrasenaActual'] = 'queque123';
$_POST['contrasenaNueva1'] = 'queque123';
$_POST['contrasenaNueva2'] = 'queque123';*/

/*$_SESSION['usuario'] = 'sergio@mail.com';
$_SESSION['rol'] = 'estudiante';
$_POST['accion'] = 'actualizar';

$_POST['form'] = 'formFoto';*/

/*$_POST['accion'] = 'consultar';
$_SESSION['usuario'] = 'profesor01@mail.com';
$_SESSION['rol'] = 'profesor';*/


$pjson = new PaqueteJSON();

try {

    (isset($_SESSION) && array_key_exists('usuario', $_SESSION)) or
        throw new Exception('No hay sesión activa.');
    $email = $_SESSION['usuario'];

    (isset($_SESSION) && array_key_exists('rol', $_SESSION)) or
        throw new Exception('Rol no reconocido.');
    $rol = $_SESSION['rol'];
    
    array_key_exists('accion', $_POST) or
        throw new Exception('Acción no reconocida.');
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'consultar':

            if ($rol === 'estudiante') {

                $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                
                $modEstudiante   = new EstudianteModelo();
                $modCuatrimestre = new CuatrimestreModelo();
                $modPrograma     = new ProgramaModelo();

                $estudiante    = $modEstudiante->consultarPorID($email, $bd);
                $programas     = $modPrograma->consultarTodo($bd);
                $cuatrimestres = $modCuatrimestre->consultarTodo($bd);

                $datos = array(
                    'usuario' => $estudiante,
                    'cuatrimestres' => $cuatrimestres,
                    'programas' => $programas
                );

                $pjson->setOk(true);
                $pjson->setDatos($datos);
                break;
            }

            if ($rol === 'profesor') {

                $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                
                $modProfesor = new ProfesorModelo();
                $modPrograma = new ProgramaModelo();

                $profesor  = $modProfesor->consultarPorID($email, $bd);
                $programas = $modPrograma->consultarTodo($bd);

                $datos = array(
                    'usuario'   => $profesor,
                    'programas' => $programas
                );

                $pjson->setOk(true);
                $pjson->setDatos($datos);
                break;
            }

            

            break;
        case 'actualizar':

            if ($rol === 'estudiante') {

                POSTClass::buscar(array('form')) or
                    throw new Exception('Parámetros insuficientes para realizar la accción.');
                $vform = POSTClass::recuperar(array('form'));
                $form = $vform['form'];

                $camposForm1 = array('nombre', 'paterno', 'materno', 'fnacimiento', 'sexo');
                $camposForm2 = array('matricula', 'programa', 'cuatrimestre');
                $camposForm3 = array('contrasenaActual', 'contrasenaNueva1', 'contrasenaNueva2');
                $camposFormF = array('foto');

                // Recibir los datos:
                switch ($form) {

                    case 'form1': // Actualiza los datos personales

                        // Búscar los campos necesarios:
                        POSTClass::buscar($camposForm1) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = POSTClass::recuperar($camposForm1);
                        
                        // Válidar los datos:
                        Validador::validarNombre($campos['nombre']);
                        Validador::validarApellido(Validador::PATERNO, $campos['paterno']);
                        Validador::validarApellido(Validador::MATERNO, $campos['materno']);
                        Validador::validarSexo($campos['sexo']);

                        // Encapsular datos en objeto:
                        $estudiante = new Estudiante();
                        $estudiante->setEmail($email);
                        $estudiante->setNombre($campos['nombre']);
                        $estudiante->setApaterno($campos['paterno']);
                        $estudiante->setAmaterno($campos['materno']);
                        $estudiante->setFnacimiento($campos['fnacimiento']);
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
                    
                    case 'form2': // Actualiza los datos de escolares:

                        // Búscar los campos necesarios:
                        POSTClass::buscar($camposForm2) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = POSTClass::recuperar($camposForm2);
                        
                        // Válidar los datos:
                        $campos['programa'] = (int)$campos['programa'];
                        $campos['cuatrimestre'] = (int)$campos['cuatrimestre'];

                        if ($campos['programa'] === 0) {
                            $campos['programa'] = null;
                        }

                        if ($campos['cuatrimestre'] === 0) {
                            $campos['cuatrimestre'] = null;
                        }

                        // Encapsular datos en objeto:
                        $estudiante = new Estudiante();
                        $estudiante->setEmail($email);
                        $estudiante->setMatricula($campos['matricula']);
                        $estudiante->setIdPrograma($campos['programa']);
                        $estudiante->setIdCuatrimestre($campos['cuatrimestre']);

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
                    case 'form3': // Actualiza la contraseña:

                        // Búscar los campos necesarios:
                        POSTClass::buscar($camposForm3) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = POSTClass::recuperar($camposForm3);
                        
                        // Válidar los datos:
                        Validador::validarContrasena($campos['contrasenaNueva1'], $campos['contrasenaNueva2']);

                        // Crear modelos necesarios:
                        $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                        $modEstudiante = new EstudianteModelo();

                        // Encapsular datos en objeto:
                        $estudiante = new Estudiante();
                        $estudiante->setEmail($email);
                        $estudiante->setContrasena($campos['contrasenaActual']);

                        // Comprobacion:
                        $contrasenaComprobada = $modEstudiante->comprobarContrasena(
                            $estudiante->getEmail(),
                            $estudiante->getContrasena(),
                            $bd
                        );

                        $contrasenaComprobada or throw new Exception('Su contraseña actual no es correcta.');

                        $campos['contrasenaActual'] = md5($campos['contrasenaActual']);
                        $campos['contrasenaNueva1'] = md5($campos['contrasenaNueva1']);
                        $campos['contrasenaNueva2'] = md5($campos['contrasenaNueva2']);

                        // Pasar contraseña ya encriptada:
                        $estudiante->setContrasena($campos['contrasenaActual']);

                        // Crear sentencia de actualización:
                        $modEstudiante->agregarActualizacion(EstudianteModelo::COL_CONTRASENA, $campos['contrasenaNueva1'], PDO::PARAM_STR);
                        
                        $modEstudiante->setClausulaWhere(EstudianteModelo::COL_EMAIL, $estudiante->getEmail(), PDO::PARAM_STR);

                        $modEstudiante->construirSql(EstudianteModelo::TABLA_USUARIOS);

                        
                        $modEstudiante->actualizar($bd);

                        $bd = null;

                        $pjson->setMensaje($modEstudiante->getReporteActualizacion());

                        unset($campos);

                        break;
                    
                    case 'formFoto': // Actualiza la foto de perfil:

                        $pjson->setMensaje('Se actualizara la foto');
                        // Búscar los campos necesarios:
                        FILESClass::buscar($camposFormF) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = FILESClass::recuperar($camposFormF);
                        $foto = $campos['foto'];

                        // Datos necesarios:
                        $ubicacionTemporal = $foto['tmp_name'];
                        $nombreOriginal    = $foto['name'];
                        $url               = '../img/fperfil/';

                        // Reglas de válidación:
                        $extensionesValidas = array('png', 'jpg', 'jpeg');
                        $tamanoMaximo = 6250000; // 50MB

                        // Extracción de la extensión:
                        $nombreArray    = explode('.', $nombreOriginal);
                        $extensionFoto  = end($nombreArray);
                        $extensionFoto  = strtolower($extensionFoto);

                        /*if ($foto['error'] ===  0) { // Archivo subido, continuar:
                            if (in_array($extensionFoto, $extensionesValidas)) { // Extensión válida:
                                if ($foto['size'] <= $tamanoMaximo) { // Tamaño válido

                                    $nuevoNombre = md5($_SESSION['usuario']) . '.' . $extensionFoto;
                                    $nuevaUbicacion = $url . $nuevoNombre;
                                    
                                    if (move_uploaded_file($ubicacionTemporal, $nuevaUbicacion)) {
                                        //$pjson->setMensaje('archivo movido  ');

                                        // Encapsular datos:
                                        $estudiante = new Estudiante();
                                        $estudiante->setEmail($email);
                                        $estudiante->setDirFoto($nuevoNombre);

                                        // Realizar la inserción en la bd:
                                        $modEstudiante = new EstudianteModelo();
                                        $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);

                                        $modEstudiante->agregarActualizacion(EstudianteModelo::COL_DIR_FOTO, $estudiante->getDirFoto(), PDO::PARAM_STR);
                                        $modEstudiante->setClausulaWhere(EstudianteModelo::COL_EMAIL, $estudiante->getEmail(), PDO::PARAM_STR);
                                        $modEstudiante->construirSql(EstudianteModelo::TABLA_USUARIOS);
                                        
                                        $modEstudiante->actualizar($bd);
                                        $bd = null;
                                        $pjson->setMensaje($modEstudiante->getReporteActualizacion());
                                    
                                    } else {
                                        throw new Exception('La imagen no pudo ser procesada.');
                                    }

                                    

                                } else {
                                    throw new Exception('Imagen demasiado grante: 50MB como maximo.');
                                }
                            } else {
                                throw new Exception('Formato no válido: Solo imagenes PNG, JPG y JPEG.');
                            }
                        } else {
                            throw new Exception('Lo sentimos, su foto no fue recibida por el servidor.');
                        }*/

                        ($foto['error'] ===  0) or // Archivo subido, continuar:
                            throw new Exception('Lo sentimos, su foto no fue recibida por el servidor.');
                            
                        (in_array($extensionFoto, $extensionesValidas)) or // Extensión válida:
                            throw new Exception('Formato no válido: Solo imagenes PNG, JPG y JPEG.');
                        
                        ($foto['size'] <= $tamanoMaximo) or // Tamaño válido
                            throw new Exception('Imagen demasiado grante: 50MB como maximo.');

                        $nuevoNombre = md5($_SESSION['usuario']) . '.' . $extensionFoto;
                        $nuevaUbicacion = $url . $nuevoNombre;
                            
                        if (move_uploaded_file($ubicacionTemporal, $nuevaUbicacion)) {

                            // Encapsular datos:
                            $estudiante = new Estudiante();
                            $estudiante->setEmail($email);
                            $estudiante->setDirFoto($nuevoNombre);

                            // Realizar la inserción en la bd:
                            $modEstudiante = new EstudianteModelo();
                            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);

                            $modEstudiante->agregarActualizacion(EstudianteModelo::COL_DIR_FOTO, $estudiante->getDirFoto(), PDO::PARAM_STR);
                            $modEstudiante->setClausulaWhere(EstudianteModelo::COL_EMAIL, $estudiante->getEmail(), PDO::PARAM_STR);
                            $modEstudiante->construirSql(EstudianteModelo::TABLA_USUARIOS);
                                
                            $modEstudiante->actualizar($bd);
                            $bd = null;
                            $pjson->setMensaje($modEstudiante->getReporteActualizacion());
                            
                        } else {
                            throw new Exception('La imagen no pudo ser procesada.');
                        }

                        break;
                    default:
                        throw new Exception('Los datos no fueron recibidos por el servidor.');
                }

                $pjson->setOk(true);

            }

            if ($rol === 'profesor') {
                //$pjson->setDatos($_POST);
                //$pjson->setOk(true);
                POSTClass::buscar(array('form')) or
                    throw new Exception('Parámetros insuficientes para realizar la accción.');
                $vform = POSTClass::recuperar(array('form'));
                $form = $vform['form'];

                $camposForm1 = array('nombre', 'paterno', 'materno', 'fnacimiento', 'sexo');
                $camposForm2 = array('cedula', 'programa2');
                $camposForm3 = array('contrasenaActual', 'contrasenaNueva1', 'contrasenaNueva2');
                $camposFormF = array('foto');

                switch ($form) {

                    case 'form1': // Actualiza los datos personales

                        // Búscar los campos necesarios:
                        POSTClass::buscar($camposForm1) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = POSTClass::recuperar($camposForm1);
                        
                        // Válidar los datos:
                        Validador::validarNombre($campos['nombre']);
                        Validador::validarApellido(Validador::PATERNO, $campos['paterno']);
                        Validador::validarApellido(Validador::MATERNO, $campos['materno']);
                        Validador::validarSexo($campos['sexo']);

                        // Encapsular datos en objeto:
                        $profesor = new Profesor();
                        $profesor->setEmail($email);
                        $profesor->setNombre($campos['nombre']);
                        $profesor->setApaterno($campos['paterno']);
                        $profesor->setAmaterno($campos['materno']);
                        $profesor->setFnacimiento($campos['fnacimiento']);
                        $profesor->setSexo($campos['sexo']);

                        unset($campos);

                        // Crear modelos necesarios:
                        $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                        $modProfesor = new ProfesorModelo();

                        // Crear sentencia de actualización:
                        $modProfesor->agregarActualizacion(ProfesorModelo::COL_NOMBRE,  $profesor->getNombre(),      PDO::PARAM_STR);
                        $modProfesor->agregarActualizacion(ProfesorModelo::COL_PATERNO, $profesor->getApaterno(),    PDO::PARAM_STR);
                        $modProfesor->agregarActualizacion(ProfesorModelo::COL_MATERNO, $profesor->getAmaterno(),    PDO::PARAM_STR);
                        $modProfesor->agregarActualizacion(ProfesorModelo::COL_FE_NAC,  $profesor->getFnacimiento(), PDO::PARAM_STR);
                        $modProfesor->agregarActualizacion(ProfesorModelo::COL_SEXO,    $profesor->getSexo(),        PDO::PARAM_STR);
                        
                        $modProfesor->setClausulaWhere(ProfesorModelo::COL_EMAIL, $profesor->getEmail(), PDO::PARAM_STR);

                        $modProfesor->construirSql(ProfesorModelo::TABLA_USUARIOS);

                        $modProfesor->actualizar($bd);

                        $bd = null;

                        $pjson->setMensaje($modProfesor->getReporteActualizacion());


                        break;
                    
                    case 'form2': // Actualiza los datos de escolares:

                        // Búscar los campos necesarios:
                        POSTClass::buscar($camposForm2) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = POSTClass::recuperar($camposForm2);
                        
                        // Válidar los datos:
                        $campos['programa2'] = (int)$campos['programa2'];
                        $campos['cedula']   = (int)$campos['cedula'];

                        if ($campos['programa2'] === 0) {
                            $campos['programa2'] = null;
                        }

                        // Encapsular datos en objeto:
                        $profesor = new Profesor();
                        $profesor->setEmail($email);
                        $profesor->setIdPrograma($campos['programa2']);
                        $profesor->setCedula($campos['cedula']);

                        unset($campos);

                        // Crear modelos necesarios:
                        $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                        $modProfesor1 = new ProfesorModelo(); // Actualizara el programa: t_usuarios
                        $modProfesor2 = new ProfesorModelo(); // Actualizara la cedula: t_profesores

                        // Crear sentencia de actualización:
                        $modProfesor1->agregarActualizacion(ProfesorModelo::COL_PROG_EDU, $profesor->getIdPrograma(), PDO::PARAM_INT);
                        $modProfesor2->agregarActualizacion(ProfesorModelo::COL_CEDULA,   $profesor->getCedula(),     PDO::PARAM_STR);
                        
                        $modProfesor1->setClausulaWhere(ProfesorModelo::COL_EMAIL,    $profesor->getEmail(), PDO::PARAM_STR);
                        $modProfesor2->setClausulaWhere(ProfesorModelo::COL_EMAIL_FK, $profesor->getEmail(), PDO::PARAM_STR);

                        $modProfesor1->construirSql(ProfesorModelo::TABLA_USUARIOS);
                        $modProfesor2->construirSql(ProfesorModelo::TABLA_PROFESORES);

                        try {

                            $bd->beginTransaction();
                                $modProfesor1->actualizar($bd);
                                $modProfesor2->actualizar($bd);
                            $bd->commit();

                        } catch (Exception $ie) {

                            $bd->rollback();
                            $bd = null;
                            throw new Exception('No es fue posible actualizar sus datos de manera segura.');

                        }

                        

                        $bd = null;

                        $pjson->setMensaje($modProfesor1->getReporteActualizacion() . '<br/>' . $modProfesor2->getReporteActualizacion());

                        break;
                    case 'form3': // Actualiza la contraseña:

                        // Búscar los campos necesarios:
                        POSTClass::buscar($camposForm3) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = POSTClass::recuperar($camposForm3);
                        
                        // Válidar los datos:
                        Validador::validarContrasena($campos['contrasenaNueva1'], $campos['contrasenaNueva2']);

                        // Crear modelos necesarios:
                        $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);
                        $modProfesor = new ProfesorModelo();

                        // Encapsular datos en objeto:
                        $profesor = new Profesor();
                        $profesor->setEmail($email);
                        $profesor->setContrasena($campos['contrasenaActual']);

                        // Comprobacion:
                        $contrasenaComprobada = $modProfesor->comprobarContrasena(
                            $profesor->getEmail(),
                            $profesor->getContrasena(),
                            $bd
                        );

                        $contrasenaComprobada or throw new Exception('Su contraseña actual no es correcta.');

                        $campos['contrasenaActual'] = md5($campos['contrasenaActual']);
                        $campos['contrasenaNueva1'] = md5($campos['contrasenaNueva1']);
                        $campos['contrasenaNueva2'] = md5($campos['contrasenaNueva2']);

                        // Pasar contraseña ya encriptada:
                        $profesor->setContrasena($campos['contrasenaActual']);

                        // Crear sentencia de actualización:
                        $modProfesor->agregarActualizacion(ProfesorModelo::COL_CONTRASENA, $campos['contrasenaNueva1'], PDO::PARAM_STR);
                        
                        $modProfesor->setClausulaWhere(ProfesorModelo::COL_EMAIL, $profesor->getEmail(), PDO::PARAM_STR);

                        $modProfesor->construirSql(ProfesorModelo::TABLA_USUARIOS);

                        
                        $modProfesor->actualizar($bd);

                        $bd = null;

                        $pjson->setMensaje($modProfesor->getReporteActualizacion());

                        unset($campos);

                        break;
                    
                    case 'formFoto': // Actualiza la foto de perfil:

                        $pjson->setMensaje('Se actualizara la foto');
                        // Búscar los campos necesarios:
                        FILESClass::buscar($camposFormF) or
                            throw new Exception('Parámetros insuficientes para realizar la accción.');
                        $campos = FILESClass::recuperar($camposFormF);
                        $foto = $campos['foto'];

                        // Datos necesarios:
                        $ubicacionTemporal = $foto['tmp_name'];
                        $nombreOriginal    = $foto['name'];
                        $url               = '../img/fperfil/';

                        // Reglas de válidación:
                        $extensionesValidas = array('png', 'jpg', 'jpeg');
                        $tamanoMaximo = 6250000; // 50MB

                        // Extracción de la extensión:
                        $nombreArray    = explode('.', $nombreOriginal);
                        $extensionFoto  = end($nombreArray);
                        $extensionFoto  = strtolower($extensionFoto);

                        ($foto['error'] ===  0) or // Archivo subido, continuar:
                            throw new Exception('Lo sentimos, su foto no fue recibida por el servidor.');
                            
                        (in_array($extensionFoto, $extensionesValidas)) or // Extensión válida:
                            throw new Exception('Formato no válido: Solo imagenes PNG, JPG y JPEG.');
                        
                        ($foto['size'] <= $tamanoMaximo) or // Tamaño válido
                            throw new Exception('Imagen demasiado grante: 50MB como maximo.');

                        $nuevoNombre = md5($_SESSION['usuario']) . '.' . $extensionFoto;
                        $nuevaUbicacion = $url . $nuevoNombre;
                            
                        if (move_uploaded_file($ubicacionTemporal, $nuevaUbicacion)) {

                            // Encapsular datos:
                            $profesor = new Profesor();
                            $profesor->setEmail($email);
                            $profesor->setDirFoto($nuevoNombre);

                            // Realizar la inserción en la bd:
                            $modProfesor = new ProfesorModelo();
                            $bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_USUARIO);

                            $modProfesor->agregarActualizacion(ProfesorModelo::COL_DIR_FOTO, $profesor->getDirFoto(), PDO::PARAM_STR);
                            $modProfesor->setClausulaWhere(ProfesorModelo::COL_EMAIL, $profesor->getEmail(), PDO::PARAM_STR);
                            $modProfesor->construirSql(ProfesorModelo::TABLA_USUARIOS);
                                
                            $modProfesor->actualizar($bd);
                            $bd = null;
                            $pjson->setMensaje($modProfesor->getReporteActualizacion());
                            
                        } else {
                            throw new Exception('La imagen no pudo ser procesada.');
                        }

                        break;
                    default:
                        throw new Exception('Los datos no fueron recibidos por el servidor.');
                    
                }

                $pjson->setOk(true);

            }

            

            break;
        default:
            throw new Exception('Acción no reconocida.');
    }


} catch(Exception $e) {

    $pjson->setOk(false);
    $pjson->setMensaje($e->getMessage());

} finally {

    print_r(json_encode($pjson));
    
}


?>