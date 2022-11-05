<?php

include_once("entidades/Token.php");

class TokenModelo {

    public function __construct() {
    }

    public function insertar($token, &$conexion) {
        $sql = "INSERT INTO reg_tokens (id, token, fecha_expiracion, tipo, usado, email)
         VALUES (0, ?, adddate(now(), INTERVAL 1 DAY), ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue(1, $token->getToken(), PDO::PARAM_STR);
            $sentencia->bindValue(2, $token->getTipo(), PDO::PARAM_STR);
            $sentencia->bindValue(3, $token->getUsado(), PDO::PARAM_STR);
            $sentencia->bindValue(4, $token->getEmail(), PDO::PARAM_STR);
        $sentencia->execute();
        if ($sentencia->rowCount() === 0) {
            throw new Exception("Cero filas afectadas al insertar el token. Rollback.");
        }
    }

}

?>