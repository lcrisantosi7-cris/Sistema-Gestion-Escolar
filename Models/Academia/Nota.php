<?php
require_once '../../Config/database.php';

class Nota {
    
    // 1. Listar Cursos del Docente (CORREGIDO)
    public function listarCursosDocente($idPersonal, $idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        
        // CORRECCIÓN: 
        // 1. Usamos MAX(a.idAsignacion) para tomar un ID único por grupo.
        // 2. Agregamos todos los campos de texto al GROUP BY para evitar el error de SQL strict mode.
        
        $sql = "SELECT MAX(a.idAsignacion) as idAsignacion, 
                       c.nombreCurso, 
                       s.nombreSeccion, g.nombreGrado, g.nivel, s.idSeccion
                FROM asignaciondocente a
                INNER JOIN curso c ON a.idCurso = c.idCurso
                INNER JOIN seccion s ON a.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE a.idPersonal = :idPers AND s.idPeriodo = :idPer
                GROUP BY c.idCurso, c.nombreCurso, s.idSeccion, s.nombreSeccion, g.nombreGrado, g.nivel
                ORDER BY g.nivel DESC, g.nombreGrado ASC, s.nombreSeccion ASC";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->execute([':idPers' => $idPersonal, ':idPer' => $idPeriodo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Obtener Datos para la Vista de Registro (SIN CAMBIOS)
    public function obtenerDatosRegistro($idAsignacion) {
        $obd = new Database();
        $obd->conectar();

        // A. Obtener Info del Curso y Sección
        $sqlInfo = "SELECT c.idCurso, c.nombreCurso, s.idSeccion, s.idPeriodo,
                           g.nombreGrado, s.nombreSeccion, g.nivel
                    FROM asignaciondocente a
                    INNER JOIN curso c ON a.idCurso = c.idCurso
                    INNER JOIN seccion s ON a.idSeccion = s.idSeccion
                    INNER JOIN grado g ON s.idGrado = g.idGrado
                    WHERE a.idAsignacion = :id";
        $stmt = $obd->conexion->prepare($sqlInfo);
        $stmt->execute([':id' => $idAsignacion]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$info) return null;

        // B. Obtener Competencias del Curso
        $sqlComp = "SELECT idCompetenciaCurso, textCompetencia FROM competenciacurso WHERE idCurso = :idCurso";
        $stmtC = $obd->conexion->prepare($sqlComp);
        $stmtC->execute([':idCurso' => $info['idCurso']]);
        $competencias = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        // C. Obtener Bimestres y su Estado
        $sqlBi = "SELECT idBimestre, nombreBimestre, estado FROM bimestre WHERE idPeriodo = :idPer ORDER BY idBimestre ASC";
        $stmtB = $obd->conexion->prepare($sqlBi);
        $stmtB->execute([':idPer' => $info['idPeriodo']]);
        $bimestres = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        // D. Obtener Alumnos Matriculados
        $sqlAlum = "SELECT m.idMatricula, p.nombres, p.apellidoPaterno, p.apellidoMaterno
                    FROM matricula m
                    INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                    INNER JOIN persona p ON e.idPersona = p.idPersona
                    WHERE m.idSeccion = :idSec AND m.estado = 'Matriculado'
                    ORDER BY p.apellidoPaterno ASC";
        $stmtA = $obd->conexion->prepare($sqlAlum);
        $stmtA->execute([':idSec' => $info['idSeccion']]);
        $alumnos = $stmtA->fetchAll(PDO::FETCH_ASSOC);

        // E. Obtener Notas Existentes
        $notasRegistradas = [];
        $sqlNotas = "SELECT cc.nota, cc.idMatricula, cc.idCompetenciaCurso, cc.idBimestre
                     FROM calificacioncurso cc
                     INNER JOIN matricula m ON cc.idMatricula = m.idMatricula
                     WHERE m.idSeccion = :idSec";
        $stmtN = $obd->conexion->prepare($sqlNotas);
        $stmtN->execute([':idSec' => $info['idSeccion']]);
        
        foreach ($stmtN->fetchAll(PDO::FETCH_ASSOC) as $n) {
            $notasRegistradas[$n['idMatricula']][$n['idCompetenciaCurso']][$n['idBimestre']] = $n['nota'];
        }

        return [
            'info' => $info,
            'competencias' => $competencias,
            'bimestres' => $bimestres,
            'alumnos' => $alumnos,
            'notas' => $notasRegistradas
        ];
    }

    // 3. Guardar Notas (SIN CAMBIOS, PERO LO INCLUYO PARA QUE TENGAS EL ARCHIVO COMPLETO)
    public function guardarNotas($datosNotas) {
        $obd = new Database();
        $obd->conectar();
        
        try {
            $obd->conexion->beginTransaction();
            
            $sql = "INSERT INTO calificacioncurso (idMatricula, idCompetenciaCurso, idBimestre, nota) 
                    VALUES (:idMat, :idComp, :idBi, :nota)
                    ON DUPLICATE KEY UPDATE nota = :notaUpd";
            
            $stmt = $obd->conexion->prepare($sql);

            foreach ($datosNotas as $idMatricula => $competencias) {
                foreach ($competencias as $idCompetencia => $bimestres) {
                    foreach ($bimestres as $idBimestre => $nota) {
                        if ($nota !== "") {
                            $stmt->execute([
                                ':idMat' => $idMatricula,
                                ':idComp' => $idCompetencia,
                                ':idBi' => $idBimestre,
                                ':nota' => $nota,
                                ':notaUpd' => $nota
                            ]);
                        }
                    }
                }
            }

            $obd->conexion->commit();
            return true;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return false;
        }
    }
}
?>