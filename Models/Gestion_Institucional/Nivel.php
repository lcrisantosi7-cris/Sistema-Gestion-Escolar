<?php
require_once '../../Config/database.php';

class Nivel {
    public $idNivel;
    public $nombreNivel;

    public function __construct($id = null, $nom = null) {
        $this->idNivel = $id;
        $this->nombreNivel = $nom;
    }

    public function listarTodos() {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM nivel";
        $result = $obd->conexion->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener el ID del nivel buscando por su nombre (ej: 'Primaria')
    public function obtenerIdPorNombre($nombre) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT idNivel FROM nivel WHERE nivel = :nom";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':nom', $nombre);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['idNivel'] : null;
    }

    
}
?>