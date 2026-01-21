<?php
require_once '../../Config/database.php';

class Seccion {
    
    public $idSeccion;
    public $nombreSeccion;
    public $vacantes;
    public $idPersonal;
    public $idGrado;
    public $idPeriodo;

    // --- CONSTRUCTOR ---
    public function __construct($nombre = null, $vac = null, $idPers = null, $idGra = null, $idPer = null, $idSec = null) {
        $this->nombreSeccion = $nombre;
        $this->vacantes = $vac;
        $this->idPersonal = $idPers;
        $this->idGrado = $idGra;
        $this->idPeriodo = $idPer;
        $this->idSeccion = $idSec;
    }

    // --- MÉTODOS DE CONSULTA (Estos devuelven arrays para la vista) ---

    public function listarPorGrado($idGrado, $idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        
        // CORRECCIÓN: 
        // 1. Agregamos INNER JOIN persona per ON p.idPersona = per.idPersona
        // 2. Seleccionamos per.nombres (de la tabla persona) en lugar de p.nombres
        $sql = "SELECT s.*, per.nombres, per.apellidoPaterno 
                FROM seccion s
                INNER JOIN personal p ON s.idPersonal = p.idPersonal
                INNER JOIN persona per ON p.idPersona = per.idPersona 
                WHERE s.idGrado = :grado AND s.idPeriodo = :periodo 
                ORDER BY s.nombreSeccion ASC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':grado', $idGrado);
        $stmt->bindParam(':periodo', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idSeccion) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM seccion WHERE idSeccion = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idSeccion);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- VALIDACIONES (Las mantengo recibiendo parámetros para ser flexibles) ---

    public function validarNombreSeccion($idSeccionExcluir = null) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT COUNT(*) as total FROM seccion 
                WHERE nombreSeccion = :nombre AND idGrado = :grado AND idPeriodo = :periodo";
        
        if ($idSeccionExcluir) {
            $sql .= " AND idSeccion != :idExcluir";
        }

        $stmt = $obd->conexion->prepare($sql);
        // Usamos los atributos del objeto
        $stmt->bindParam(':nombre', $this->nombreSeccion);
        $stmt->bindParam(':grado', $this->idGrado);
        $stmt->bindParam(':periodo', $this->idPeriodo);
        
        if ($idSeccionExcluir) $stmt->bindParam(':idExcluir', $idSeccionExcluir);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function validarDocenteOcupado($idSeccionExcluir = null) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT COUNT(*) as total FROM seccion 
                WHERE idPersonal = :personal AND idPeriodo = :periodo";
        
        if ($idSeccionExcluir) {
            $sql .= " AND idSeccion != :idExcluir";
        }

        $stmt = $obd->conexion->prepare($sql);
        // Usamos los atributos del objeto
        $stmt->bindParam(':personal', $this->idPersonal);
        $stmt->bindParam(':periodo', $this->idPeriodo);
        
        if ($idSeccionExcluir) $stmt->bindParam(':idExcluir', $idSeccionExcluir);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    // --- GUARDAR (USANDO $THIS->) ---
    public function guardar() {
        $obd = new Database();
        $obd->conectar();
        
        try {
            if (!empty($this->idSeccion)) {
                // UPDATE
                $sql = "UPDATE seccion SET nombreSeccion = :nombre, vacantes = :vacantes, idPersonal = :personal 
                        WHERE idSeccion = :id";
                $stmt = $obd->conexion->prepare($sql);
                $stmt->bindParam(':nombre', $this->nombreSeccion);
                $stmt->bindParam(':vacantes', $this->vacantes);
                $stmt->bindParam(':personal', $this->idPersonal);
                $stmt->bindParam(':id', $this->idSeccion);
            } else {
                // INSERT
                $sql = "INSERT INTO seccion (nombreSeccion, vacantes, idPersonal, idGrado, idPeriodo) 
                        VALUES (:nombre, :vacantes, :personal, :grado, :periodo)";
                $stmt = $obd->conexion->prepare($sql);
                $stmt->bindParam(':nombre', $this->nombreSeccion);
                $stmt->bindParam(':vacantes', $this->vacantes);
                $stmt->bindParam(':personal', $this->idPersonal);
                $stmt->bindParam(':grado', $this->idGrado);
                $stmt->bindParam(':periodo', $this->idPeriodo);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            return "Error BD: " . $e->getMessage();
        }
    }

    public function eliminar($id) {
        $obd = new Database();
        $obd->conectar();
        $sql = "DELETE FROM seccion WHERE idSeccion = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
// Listar secciones del periodo activo con el dato del Nivel (Primaria/Secundaria)
    public function listarParaAsignacion($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT s.idSeccion, s.nombreSeccion, g.nombreGrado, g.nivel 
                FROM seccion s
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE s.idPeriodo = :periodo
                ORDER BY g.nivel DESC, g.nombreGrado ASC, s.nombreSeccion ASC";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':periodo', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar secciones con conteo de inscritos para validar vacantes
    public function listarConVacantes($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        
        // IMPORTANTE: Seleccionamos s.idGrado para poder filtrar por nivel de promoción
        $sql = "SELECT s.idSeccion, s.nombreSeccion, s.vacantes, s.idGrado,
                       g.nombreGrado, g.nivel,
                       (SELECT COUNT(*) FROM matricula m WHERE m.idSeccion = s.idSeccion AND m.estado = 'Matriculado') as inscritos
                FROM seccion s
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE s.idPeriodo = :periodo
                ORDER BY g.nivel DESC, g.nombreGrado ASC, s.nombreSeccion ASC";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':periodo', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validación de seguridad para el backend antes de guardar
    public function verificarCupoDisponible($idSeccion) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT vacantes, 
                (SELECT COUNT(*) FROM matricula WHERE idSeccion = :id AND estado = 'Matriculado') as inscritos
                FROM seccion WHERE idSeccion = :id";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idSeccion);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($res) {
            return $res['inscritos'] < $res['vacantes'];
        }
        return false;
    }
}
?>