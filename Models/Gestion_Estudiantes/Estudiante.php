<?php
require_once '../../Config/database.php';

class Estudiante {
    public function buscarPorDni($dni) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT e.idEstudiante, e.idPersona, e.idApoderado,
                       p.dni, p.nombres, p.apellidoPaterno, p.apellidoMaterno, p.fechaNacimiento, p.genero,
                       (SELECT idMatricula FROM matricula WHERE idEstudiante = e.idEstudiante ORDER BY idMatricula DESC LIMIT 1) as ultimaMatricula
                FROM estudiante e
                INNER JOIN persona p ON e.idPersona = p.idPersona
                WHERE p.dni = :dni";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarNuevo($datosPer, $idApoderado, $edad) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            // 1. PERSONA ESTUDIANTE: (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento)
            // Nota: 'direccion' es obligatoria, pasamos un guión '-'
            $sqlP = "INSERT INTO persona (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento) 
                     VALUES (?, ?, ?, ?, ?, '-', ?)";
            
            $stmtP = $obd->conexion->prepare($sqlP);
            // ORDEN EXACTO:
            $stmtP->execute([
                $datosPer['dni'], 
                $datosPer['nombres'], 
                $datosPer['paterno'], 
                $datosPer['materno'], 
                $datosPer['genero'], 
                $datosPer['nacimiento']
            ]);
            $idPersona = $obd->conexion->lastInsertId();

            // 2. ESTUDIANTE: (edad, idPersona, idApoderado)
            $sqlE = "INSERT INTO estudiante (edad, idPersona, idApoderado) VALUES (?, ?, ?)";
            $stmtE = $obd->conexion->prepare($sqlE);
            $stmtE->execute([$edad, $idPersona, $idApoderado]);
            $idEstudiante = $obd->conexion->lastInsertId();

            $obd->conexion->commit();
            return $idEstudiante;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return false;
        }
    }
}
?>