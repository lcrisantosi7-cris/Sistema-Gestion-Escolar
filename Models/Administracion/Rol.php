<?php
require_once '../../Config/database.php';

class Rol {
    public function listarTodos() {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM rol";
        $result = $obd->conexion->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>