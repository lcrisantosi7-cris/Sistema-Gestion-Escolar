<?php
require_once '../../Config/database.php';

class Apoderado {
    
    public function buscarPorDni($dni) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT a.idApoderado, a.ocupacion, a.correo, a.telefono, 
                       p.idPersona, p.dni, p.nombres, p.apellidoPaterno, p.apellidoMaterno, p.direccion
                FROM apoderado a
                INNER JOIN persona p ON a.idPersona = p.idPersona
                WHERE p.dni = :dni";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarNuevo($datosPer, $datosApo) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            // 1. PERSONA: (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento)
            // Nota: Para apoderado asignamos 'M' y fecha default porque son obligatorios en BD pero no los pides en form
            $sqlP = "INSERT INTO persona (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento) 
                     VALUES (?, ?, ?, ?, 'M', ?, '1980-01-01')";
            
            $stmtP = $obd->conexion->prepare($sqlP);
            // EL ORDEN ES CRÍTICO AQUÍ:
            $stmtP->execute([
                $datosPer['dni'], 
                $datosPer['nombres'], 
                $datosPer['paterno'], 
                $datosPer['materno'], 
                $datosPer['direccion']
            ]);
            $idPersona = $obd->conexion->lastInsertId();

            // 2. APODERADO: (idPersona, ocupacion, correo, telefono)
            $sqlA = "INSERT INTO apoderado (idPersona, ocupacion, correo, telefono) VALUES (?, ?, ?, ?)";
            $stmtA = $obd->conexion->prepare($sqlA);
            $stmtA->execute([
                $idPersona, 
                $datosApo['ocupacion'], 
                $datosApo['correo'], 
                $datosApo['telefono']
            ]);
            $idApoderado = $obd->conexion->lastInsertId();

            $obd->conexion->commit();
            return $idApoderado;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return false; 
        }
    }
}
?>