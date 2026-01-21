<?php
require_once '../../Config/database.php';

class Boleta {
    
    // 1. Listar Estudiantes Matriculados en el Periodo Activo (Para el Index)
    public function listarEstudiantes($idPeriodo, $filtros = []) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT m.idMatricula, 
                       p.dni, p.nombres, p.apellidoPaterno, p.apellidoMaterno,
                       s.nombreSeccion, g.nombreGrado, g.nivel
                FROM matricula m
                INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                INNER JOIN persona p ON e.idPersona = p.idPersona
                INNER JOIN seccion s ON m.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                WHERE s.idPeriodo = :periodo AND m.estado = 'Matriculado'";

        // Filtros opcionales
        if (!empty($filtros['nivel'])) {
            $sql .= " AND g.nivel = '" . $filtros['nivel'] . "'";
        }
        if (!empty($filtros['grado'])) {
            $sql .= " AND g.idGrado = " . $filtros['grado'];
        }
        if (!empty($filtros['seccion'])) {
            $sql .= " AND s.idSeccion = " . $filtros['seccion'];
        }

        $sql .= " ORDER BY g.nivel DESC, g.nombreGrado ASC, s.nombreSeccion ASC, p.apellidoPaterno ASC";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':periodo', $idPeriodo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Obtener Datos de Cabecera (Alumno, Tutor, Grado)
    public function obtenerCabecera($idMatricula) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT m.idMatricula, 
                       pEst.dni, pEst.nombres as nomEst, pEst.apellidoPaterno as apePatEst, pEst.apellidoMaterno as apeMatEst,
                       g.nombreGrado, g.nivel, s.nombreSeccion,
                       pTut.nombres as nomTut, pTut.apellidoPaterno as apePatTut, pTut.apellidoMaterno as apeMatTut
                FROM matricula m
                INNER JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                INNER JOIN persona pEst ON e.idPersona = pEst.idPersona
                INNER JOIN seccion s ON m.idSeccion = s.idSeccion
                INNER JOIN grado g ON s.idGrado = g.idGrado
                -- Join para obtener el Tutor (Personal -> Persona)
                LEFT JOIN personal per ON s.idPersonal = per.idPersonal
                LEFT JOIN persona pTut ON per.idPersona = pTut.idPersona
                WHERE m.idMatricula = :id";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idMatricula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Obtener Cursos y Competencias según el Nivel
    // Devuelve estructura jerárquica: Cursos -> Competencias
    public function obtenerMallaCurricular($nivelNombre) {
        $obd = new Database();
        $obd->conectar();
        
        // Obtenemos ID nivel (asumiendo tabla nivel)
        $stmtNiv = $obd->conexion->prepare("SELECT idNivel FROM nivel WHERE nivel = ?");
        $stmtNiv->execute([$nivelNombre]);
        $idNivel = $stmtNiv->fetchColumn();

        if(!$idNivel) return [];

        // Traemos cursos
        $sqlCursos = "SELECT idCurso, nombreCurso FROM curso WHERE idNivel = ? ORDER BY nombreCurso ASC";
        $stmtC = $obd->conexion->prepare($sqlCursos);
        $stmtC->execute([$idNivel]);
        $cursos = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        // Para cada curso, traemos sus competencias
        foreach ($cursos as &$c) {
            $sqlComp = "SELECT idCompetenciaCurso, textCompetencia FROM competenciacurso WHERE idCurso = ?";
            $stmtComp = $obd->conexion->prepare($sqlComp);
            $stmtComp->execute([$c['idCurso']]);
            $c['competencias'] = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
        }
        return $cursos;
    }

    // 4. Obtener TODAS las notas de un alumno en una sola consulta eficiente
    // Retorna array: [idCompetencia][NombreBimestre] = Nota
    public function obtenerNotas($idMatricula) {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT cc.idCompetenciaCurso, cc.nota, b.nombreBimestre
                FROM calificacioncurso cc
                INNER JOIN bimestre b ON cc.idBimestre = b.idBimestre
                WHERE cc.idMatricula = :id";
                
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idMatricula);
        $stmt->execute();
        
        $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Reorganizar para fácil acceso en la vista
        $notasOrganizadas = [];
        foreach($raw as $r) {
            // Ejemplo: $notas[5]['I Bimestre'] = 15;
            $notasOrganizadas[$r['idCompetenciaCurso']][$r['nombreBimestre']] = $r['nota'];
        }
        return $notasOrganizadas;
    }

    // 5. Obtener Conducta
    public function obtenerConducta($idMatricula) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT ec.nombreCondcuta, ec.observacion, b.nombreBimestre 
                FROM evaluacionconducta ec
                INNER JOIN bimestre b ON ec.idBimestre = b.idBimestre
                WHERE ec.idMatricula = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idMatricula);
        $stmt->execute();
        
        $conducta = [];
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $conducta[$row['nombreBimestre']] = $row['nombreCondcuta'];
        }
        return $conducta;
    }
}
?>