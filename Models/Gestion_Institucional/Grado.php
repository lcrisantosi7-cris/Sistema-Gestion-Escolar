<?php
require_once '../../Config/database.php';

class Grado {
    public $idGrado;
    public $nombreGrado;
    public $nivel;

    public function __construct($id = null, $nombre = null, $niv = null) {
        $this->idGrado = $id;
        $this->nombreGrado = $nombre;
        $this->nivel = $niv;
    }

    // 3. Método Listar (Este devuelve un array asociativo PDO, es lo estándar)
    public function listarTodos() {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM grado ORDER BY nivel ASC, nombreGrado ASC";
        $result = $obd->conexion->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>