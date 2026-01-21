<?php
require_once '../../Config/database.php';

class Competencia {
    public $idCompetencia;
    public $descripcion; // 'textCompetencia' en tu BD
    public $idCurso;

    public function __construct($id = null, $desc = null, $idCur = null) {
        $this->idCompetencia = $id;
        $this->descripcion = $desc;
        $this->idCurso = $idCur;
    }

    public function listarPorCurso($idCurso) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM competenciacurso WHERE idCurso = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idCurso);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM competenciacurso WHERE idCompetenciaCurso = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar() {
        $obd = new Database();
        $obd->conectar();
        if ($this->idCompetencia) {
            // UPDATE
            $sql = "UPDATE competenciacurso SET textCompetencia = :txt WHERE idCompetenciaCurso = :id";
            $stmt = $obd->conexion->prepare($sql);
            $stmt->bindParam(':txt', $this->descripcion);
            $stmt->bindParam(':id', $this->idCompetencia);
        } else {
            // INSERT
            $sql = "INSERT INTO competenciacurso (textCompetencia, idCurso) VALUES (:txt, :idCurso)";
            $stmt = $obd->conexion->prepare($sql);
            $stmt->bindParam(':txt', $this->descripcion);
            $stmt->bindParam(':idCurso', $this->idCurso);
        }
        return $stmt->execute();
    }

    public function eliminar($id) {
        $obd = new Database();
        $obd->conectar();
        $sql = "DELETE FROM competenciacurso WHERE idCompetenciaCurso = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>