<?php
require_once '../../Config/database.php';

class Asignacion {
    public $idAsignacion;
    public $horaInicio;
    public $horaFin;
    public $diaSemana;
    public $idPersonal;
    public $idCurso;
    public $idSeccion;

    public function __construct($id=null, $hIni=null, $hFin=null, $dia=null, $idPers=null, $idCur=null, $idSec=null) {
        $this->idAsignacion = $id;
        $this->horaInicio = $hIni;
        $this->horaFin = $hFin;
        $this->diaSemana = $dia;
        $this->idPersonal = $idPers;
        $this->idCurso = $idCur;
        $this->idSeccion = $idSec;
    }

    // Listar todo general
    public function listarPorPeriodo($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT a.*, per.nombres, per.apellidoPaterno, c.nombreCurso, s.nombreSeccion, g.nombreGrado, g.nivel
                FROM asignaciondocente a
                INNER JOIN personal p ON a.idPersonal = p.idPersonal
                INNER JOIN persona per ON p.idPersona = per.idPersona
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN seccion s ON a.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE s.idPeriodo = :periodo
                ORDER BY a.diaSemana DESC, a.horaInicio ASC";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':periodo', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NUEVO: Listar Horario Específico de una Sección ---
    public function listarHorarioSeccion($idSeccion) {
        $obd = new Database();
        $obd->conectar();
        // Ordenamos por día y hora para pintarlo bien
        $sql = "SELECT a.*, c.nombreCurso, per.nombres, per.apellidoPaterno
                FROM asignaciondocente a
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN personal p ON a.idPersonal = p.idPersonal
                INNER JOIN persona per ON p.idPersona = per.idPersona
                WHERE a.idSeccion = :idSec
                ORDER BY FIELD(a.diaSemana, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'), a.horaInicio ASC";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':idSec', $idSeccion);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM asignaciondocente WHERE idAsignacion = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- VALIDACIÓN 1: CRUCE DE DOCENTE (El profe no puede duplicarse) ---
    public function validarCruceDocente($idExcluir = null) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT COUNT(*) as total FROM asignaciondocente 
                WHERE idPersonal = :personal AND diaSemana = :dia
                AND (:inicio < horaFin AND :fin > horaInicio)"; 

        if ($idExcluir) $sql .= " AND idAsignacion != :idExcluir";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':personal', $this->idPersonal);
        $stmt->bindParam(':dia', $this->diaSemana);
        $stmt->bindParam(':inicio', $this->horaInicio);
        $stmt->bindParam(':fin', $this->horaFin);
        if ($idExcluir) $stmt->bindParam(':idExcluir', $idExcluir);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    // --- VALIDACIÓN 2: CRUCE DE SECCIÓN (La clase no puede tener 2 cursos a la vez) ---
    public function validarCruceSeccion($idExcluir = null) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT COUNT(*) as total FROM asignaciondocente 
                WHERE idSeccion = :seccion AND diaSemana = :dia
                AND (:inicio < horaFin AND :fin > horaInicio)"; 

        if ($idExcluir) $sql .= " AND idAsignacion != :idExcluir";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':seccion', $this->idSeccion); // Usamos la sección del objeto
        $stmt->bindParam(':dia', $this->diaSemana);
        $stmt->bindParam(':inicio', $this->horaInicio);
        $stmt->bindParam(':fin', $this->horaFin);
        if ($idExcluir) $stmt->bindParam(':idExcluir', $idExcluir);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function guardar() {
        $obd = new Database();
        $obd->conectar();
        try {
            if ($this->idAsignacion) {
                // UPDATE
                $sql = "UPDATE asignaciondocente 
                        SET horaInicio=:ini, horaFin=:fin, diaSemana=:dia, idPersonal=:pers, idCurso=:cur, idSeccion=:sec 
                        WHERE idAsignacion=:id";
                $stmt = $obd->conexion->prepare($sql);
                $stmt->bindParam(':ini', $this->horaInicio);
                $stmt->bindParam(':fin', $this->horaFin);
                $stmt->bindParam(':dia', $this->diaSemana);
                $stmt->bindParam(':pers', $this->idPersonal);
                $stmt->bindParam(':cur', $this->idCurso);
                $stmt->bindParam(':sec', $this->idSeccion);
                $stmt->bindParam(':id', $this->idAsignacion);
            } else {
                // INSERT
                $sql = "INSERT INTO asignaciondocente (horaInicio, horaFin, diaSemana, idPersonal, idCurso, idSeccion) 
                        VALUES (:ini, :fin, :dia, :pers, :cur, :sec)";
                $stmt = $obd->conexion->prepare($sql);
                $stmt->bindParam(':ini', $this->horaInicio);
                $stmt->bindParam(':fin', $this->horaFin);
                $stmt->bindParam(':dia', $this->diaSemana);
                $stmt->bindParam(':pers', $this->idPersonal);
                $stmt->bindParam(':cur', $this->idCurso);
                $stmt->bindParam(':sec', $this->idSeccion);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $obd = new Database();
        $obd->conectar();
        $sql = "DELETE FROM asignaciondocente WHERE idAsignacion = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Listar clases de un docente específico para un día específico
    public function listarClasesDocenteDia($idPersonal, $diaSemana, $idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT a.horaInicio, a.horaFin, 
                       c.nombreCurso, 
                       s.nombreSeccion, g.nombreGrado, g.nivel
                FROM asignaciondocente a
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN seccion s ON a.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE a.idPersonal = :idPers 
                  AND a.diaSemana = :dia 
                  AND s.idPeriodo = :periodo
                ORDER BY a.horaInicio ASC";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':idPers', $idPersonal);
        $stmt->bindParam(':dia', $diaSemana);
        $stmt->bindParam(':periodo', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // --- NUEVO: Obtener todo el horario de un docente específico ---
    public function listarHorarioDocente($idPersonal, $idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT a.diaSemana, a.horaInicio, a.horaFin,
                       c.nombreCurso, 
                       s.nombreSeccion, g.nombreGrado, g.nivel
                FROM asignaciondocente a
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN seccion s ON a.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE a.idPersonal = :idPers AND s.idPeriodo = :idPer
                -- Ordenar por día de la semana (Lunes a Viernes) y luego por hora
                ORDER BY FIELD(a.diaSemana, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'), a.horaInicio ASC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->execute([':idPers' => $idPersonal, ':idPer' => $idPeriodo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>