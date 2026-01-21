<?php
require_once '../../Config/database.php';

class Curso {
    public $idCurso;
    public $nombreCurso;
    public $idNivel;

    public function __construct($id = null, $nom = null, $idn = null) {
        $this->idCurso = $id;
        $this->nombreCurso = $nom;
        $this->idNivel = $idn;
    }

    // Listar cursos con el nombre del nivel (Búsqueda opcional)
    public function listar($busqueda = "") {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT c.*, n.nivel as nombreNivel 
                FROM curso c 
                INNER JOIN nivel n ON c.idNivel = n.idNivel";
        
        if ($busqueda != "") {
            $sql .= " WHERE c.nombreCurso LIKE :busqueda";
        }
        
        #$sql .= " ORDER BY c.nombreCurso ASC";

        $stmt = $obd->conexion->prepare($sql);
        
        if ($busqueda != "") {
            $param = "%" . $busqueda . "%";
            $stmt->bindParam(':busqueda', $param);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $obd = new Database();
        $obd->conectar();
        // Traemos también el nombre del nivel para mostrarlo en detalles
        $sql = "SELECT c.*, n.nivel as nombreNivel 
                FROM curso c 
                INNER JOIN nivel n ON c.idNivel = n.idNivel
                WHERE c.idCurso = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar() {
        $obd = new Database();
        $obd->conectar();
        if ($this->idCurso) {
            $sql = "UPDATE curso SET nombreCurso = :nom, idNivel = :idn WHERE idCurso = :id";
            $stmt = $obd->conexion->prepare($sql);
            $stmt->bindParam(':nom', $this->nombreCurso);
            $stmt->bindParam(':idn', $this->idNivel);
            $stmt->bindParam(':id', $this->idCurso);
        } else {
            $sql = "INSERT INTO curso (nombreCurso, idNivel) VALUES (:nom, :idn)";
            $stmt = $obd->conexion->prepare($sql);
            $stmt->bindParam(':nom', $this->nombreCurso);
            $stmt->bindParam(':idn', $this->idNivel);
        }
        
        if ($stmt->execute()) {
            // Si es insert, retornamos el ID creado para poder agregar competencias
            return $this->idCurso ? $this->idCurso : $obd->conexion->lastInsertId();
        }
        return false;
    }

    public function eliminar($id) {
        $obd = new Database();
        $obd->conectar();
        // Nota: Si tienes FK constraint en la BD, primero borra competencias
        // Aquí borramos competencias primero manualmente por seguridad
        $sqlComp = "DELETE FROM competenciacurso WHERE idCurso = :id";
        $stmtComp = $obd->conexion->prepare($sqlComp);
        $stmtComp->bindParam(':id', $id);
        $stmtComp->execute();

        $sql = "DELETE FROM curso WHERE idCurso = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function listarPorNivel($idNivel) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM curso WHERE idNivel = :id ORDER BY nombreCurso ASC";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idNivel);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>