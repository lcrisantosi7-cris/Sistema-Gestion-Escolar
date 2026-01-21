<?php
require_once '../../Config/database.php';

class Matricula {
    public function verificarSituacion($idMatriculaAnterior) {
        $obd = new Database();
        $obd->conectar();

        // Obtener grado
        $sqlInfo = "SELECT s.idGrado FROM matricula m INNER JOIN seccion s ON m.idSeccion = s.idSeccion WHERE m.idMatricula = ?";
        $stmt = $obd->conexion->prepare($sqlInfo);
        $stmt->execute([$idMatriculaAnterior]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$info) return ['estado' => 'Nuevo', 'idGradoSugerido' => 0]; 

        $idGradoActual = $info['idGrado'];
        $aprobado = true;

        // Evaluar Cursos
        $sqlC = "SELECT AVG(nota) as promedio FROM calificacioncurso WHERE idMatricula = ? GROUP BY idCompetenciaCurso";
        $stmtC = $obd->conexion->prepare($sqlC);
        $stmtC->execute([$idMatriculaAnterior]);
        $notasC = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        if (count($notasC) == 0) return ['estado' => 'SinNotas', 'idGradoSugerido' => 0];

        foreach($notasC as $n) {
            if (round($n['promedio']) < 11) $aprobado = false;
        }

        // Evaluar Transversales
        $sqlT = "SELECT AVG(nota) as promedio FROM calificaciontransversal WHERE idMatricula = ? GROUP BY idCompetenciaTransversal";
        $stmtT = $obd->conexion->prepare($sqlT);
        $stmtT->execute([$idMatriculaAnterior]);
        $notasT = $stmtT->fetchAll(PDO::FETCH_ASSOC);

        foreach($notasT as $nt) {
            if (round($nt['promedio']) < 11) $aprobado = false;
        }

        if ($aprobado) {
            $siguiente = ($idGradoActual >= 11) ? 'Egresado' : 'Promovido';
            return ['estado' => $siguiente, 'idGradoSugerido' => $idGradoActual + 1];
        } else {
            return ['estado' => 'Repitente', 'idGradoSugerido' => $idGradoActual];
        }
    }

    public function registrar($idEst, $idSec, $docs) {
        $obd = new Database();
        $obd->conectar();
        $fecha = date('Y-m-d H:i:s');
        $sql = "INSERT INTO matricula (fecha, estado, idSeccion, idEstudiante, doc_ficha_matricula, doc_copia_dni, doc_certificado_estudios, doc_partida_nacimiento)
                VALUES (?, 'Matriculado', ?, ?, ?, ?, ?, ?)";
        $stmt = $obd->conexion->prepare($sql);
        return $stmt->execute([$fecha, $idSec, $idEst, $docs['ficha'], $docs['dni'], $docs['cert'], $docs['part']]);
    }

    // Listar estudiantes matriculados en el periodo activo (con filtro opcional de sección)
    public function listarMatriculados($idPeriodo, $idSeccionFiltro = null) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT m.idMatricula, m.fecha, m.idSeccion,
                       p.dni, p.nombres, p.apellidoPaterno, p.apellidoMaterno,
                       s.nombreSeccion, g.nombreGrado, g.nivel
                FROM matricula m
                INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                INNER JOIN persona p ON e.idPersona = p.idPersona
                INNER JOIN seccion s ON m.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE s.idPeriodo = :periodo AND m.estado = 'Matriculado'";

        if ($idSeccionFiltro) {
            $sql .= " AND m.idSeccion = :seccion";
        }

        $sql .= " ORDER BY g.nivel DESC, g.nombreGrado ASC, s.nombreSeccion ASC, p.apellidoPaterno ASC";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':periodo', $idPeriodo);
        if ($idSeccionFiltro) {
            $stmt->bindParam(':seccion', $idSeccionFiltro);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una matrícula específica para editar
    public function obtenerPorId($idMatricula) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT m.*, p.nombres, p.apellidoPaterno, p.apellidoMaterno 
                FROM matricula m
                INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                INNER JOIN persona p ON e.idPersona = p.idPersona
                WHERE m.idMatricula = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idMatricula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar datos de la matrícula (Cambio de sección o docs)
    public function actualizar($idMatricula, $idSeccion, $docs) {
        $obd = new Database();
        $obd->conectar();
        $sql = "UPDATE matricula SET idSeccion = :sec, 
                doc_ficha_matricula=:d1, doc_copia_dni=:d2, doc_certificado_estudios=:d3, doc_partida_nacimiento=:d4
                WHERE idMatricula = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':sec', $idSeccion);
        $stmt->bindParam(':d1', $docs['ficha']);
        $stmt->bindParam(':d2', $docs['dni']);
        $stmt->bindParam(':d3', $docs['cert']);
        $stmt->bindParam(':d4', $docs['part']);
        $stmt->bindParam(':id', $idMatricula);
        return $stmt->execute();
    }
}
?>