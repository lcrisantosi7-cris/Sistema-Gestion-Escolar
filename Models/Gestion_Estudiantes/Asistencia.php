<?php
require_once '../../Config/database.php';

class Asistencia {
    
    // 1. Listar Cargas del Docente (Para que elija dónde marcar)
    public function listarCargasDocente($idPersonal, $idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT a.idAsignacion, a.diaSemana, a.horaInicio, a.horaFin,
                       c.nombreCurso, s.nombreSeccion, g.nombreGrado, g.nivel, s.idSeccion
                FROM asignaciondocente a
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN seccion s ON a.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE a.idPersonal = :idPers AND s.idPeriodo = :idPer
                ORDER BY a.diaSemana ASC, a.horaInicio ASC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':idPers', $idPersonal);
        $stmt->bindParam(':idPer', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Obtener Alumnos de una Sección (Para generar la lista)
    public function listarAlumnosPorSeccion($idSeccion) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT m.idMatricula, p.nombres, p.apellidoPaterno, p.apellidoMaterno
                FROM matricula m
                INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                INNER JOIN persona p ON e.idPersona = p.idPersona
                WHERE m.idSeccion = :idSec AND m.estado = 'Matriculado'
                ORDER BY p.apellidoPaterno ASC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':idSec', $idSeccion);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Registrar Asistencia (Bloque masivo)
    public function registrar($idAsignacion, $fecha, $listaAsistencia) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();
            
            // Verificamos si ya existe asistencia para esa fecha y asignación (Evitar duplicados)
            $check = $obd->conexion->prepare("SELECT count(*) FROM asistencia WHERE idAsignacion = ? AND DATE(fechaHora) = ?");
            $check->execute([$idAsignacion, $fecha]);
            if ($check->fetchColumn() > 0) {
                return "DUPLICADO";
            }

            $sql = "INSERT INTO asistencia (fechaHora, estado, idAsignacion, idMatricula) 
                    VALUES (:fecha, :estado, :idAsig, :idMat)";
            $stmt = $obd->conexion->prepare($sql);
            
            // $fecha viene 'YYYY-MM-DD', le agregamos la hora actual para el DATETIME
            $fechaHora = $fecha . ' ' . date('H:i:s');

            foreach ($listaAsistencia as $idMatricula => $estado) {
                $stmt->bindParam(':fecha', $fechaHora);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':idAsig', $idAsignacion);
                $stmt->bindParam(':idMat', $idMatricula);
                $stmt->execute();
            }

            $obd->conexion->commit();
            return true;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return false;
        }
    }

    // 4. Ver Historial (Buscar fechas registradas)
    public function listarFechasRegistradas($idAsignacion) {
        $obd = new Database();
        $obd->conectar();
        
        // Hacemos un JOIN complejo para encontrar hermanas de la asignación
        // "Buscame las asistencias donde el Curso, Sección y Profe sean iguales al de la asignación actual"
        $sql = "SELECT DISTINCT DATE(a.fechaHora) as fecha 
                FROM asistencia a
                INNER JOIN asignaciondocente ad_actual ON a.idAsignacion = ad_actual.idAsignacion
                INNER JOIN asignaciondocente ad_filtro ON ad_filtro.idAsignacion = :id
                WHERE ad_actual.idCurso = ad_filtro.idCurso 
                  AND ad_actual.idSeccion = ad_filtro.idSeccion
                  AND ad_actual.idPersonal = ad_filtro.idPersonal
                ORDER BY fecha DESC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idAsignacion);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Ver Detalle de Asistencia de una Fecha (Para editar)
    public function obtenerDetalleFecha($idAsignacion, $fecha) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT a.idAsistencia, a.estado, m.idMatricula, 
                       p.nombres, p.apellidoPaterno, p.apellidoMaterno
                FROM asistencia a
                INNER JOIN asignaciondocente ad_actual ON a.idAsignacion = ad_actual.idAsignacion
                INNER JOIN asignaciondocente ad_filtro ON ad_filtro.idAsignacion = :idAsig
                INNER JOIN matricula m ON a.idMatricula = m.idMatricula
                INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                INNER JOIN persona p ON e.idPersona = p.idPersona
                WHERE ad_actual.idCurso = ad_filtro.idCurso 
                  AND ad_actual.idSeccion = ad_filtro.idSeccion
                  AND DATE(a.fechaHora) = :fecha
                ORDER BY p.apellidoPaterno ASC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':idAsig', $idAsignacion);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Actualizar Asistencia Individual
    public function actualizarEstado($idAsistencia, $nuevoEstado) {
        $obd = new Database();
        $obd->conectar();
        $sql = "UPDATE asistencia SET estado = :est WHERE idAsistencia = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':est', $nuevoEstado);
        $stmt->bindParam(':id', $idAsistencia);
        return $stmt->execute();
    }
}
?>