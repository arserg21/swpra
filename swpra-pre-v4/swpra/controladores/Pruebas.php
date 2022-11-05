<?php
/*
class A {

    public const PROPIEDAD = 'a';
    protected $a = 'a';

    public function __call($nombre, $args) {
        if ($nombre === 'get') {
            $propiedad = $args[0];
            $propiedad === self::PROPIEDAD or
            throw new Exception('Obteniendo propiedad inexistente.');
            return $this->{$propiedad};
        }
    }

}

class B extends A {

    public const PROPIEDAD = 'b';
    protected $b = 'b';

    public function __call($nombre, $args) {
        if ($nombre === 'get') {
            $propiedad = $args[0];
            $propiedad === self::PROPIEDAD or
            parent::__call($nombre, $args);
            return $this->{$propiedad};
        }
    }

}

class C extends B {

    public const PROPIEDAD = 'c';
    protected $c = 'c';

    public function __call($nombre, $args) {
        if ($nombre === 'get') {
            $propiedad = $args[0];
            $propiedad === self::PROPIEDAD or
            parent::__call($nombre, $args);
            return $this->{$propiedad};
        }
    }

}

$c = new C();

try {
    echo $c->get(true);
} catch (Exception $e) {
    echo $e->getMessage();
}
*/
?>

<?php

/*require_once('../modelos/EstudianteModelo.php');
require_once('../modelos/ConexionModelo.php');

$mod = new EstudianteModelo();

$mod->afAgregarCampo(EstudianteModelo::COL_NOMBRE, 'otronombre', PDO::PARAM_STR);
$mod->afSetWhere(EstudianteModelo::COL_EMAIL, 'sergio@mail.com', PDO::PARAM_STR);
$sql = $mod->afConstruirSql(EstudianteModelo::TABLA_USUARIOS);

$bd = ConexionModelo::getConexion(ConexionModelo::CONEXION_BASICA);

$mod->afActualizar($bd);

$bd = null;*/


/*
if (true) {
    try {
        
        if (true) {
            $a = 1;
        }
        
        throw new Exception('dhgsvf');
    } catch(Exception $e) {
        echo $a;
    }
}*/

function a() {
    try {
        throw new Exception('E1');
    } catch (Exception $e) {
        throw new Exception('E2', 1, $e);
    }
}

try {
    a();
} catch (Exception $e) {
    print_r($e->getMessage());
    print_r('anterior: ' . $e->getPrevious()->getMessage());
}

print_r(explode('.', 'jhsdbsd.fhbfsf.jbfs'));

?>