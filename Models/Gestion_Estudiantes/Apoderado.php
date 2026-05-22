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

            // RETURNING en lugar de lastInsertId()
            $sqlP = "INSERT INTO persona (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento)
                     VALUES (?, ?, ?, ?, 'M', ?, '1980-01-01')
                     RETURNING idPersona";
            $stmtP = $obd->conexion->prepare($sqlP);
            $stmtP->execute([
                $datosPer['dni'],
                $datosPer['nombres'],
                $datosPer['paterno'],
                $datosPer['materno'],
                $datosPer['direccion']
            ]);
            $idPersona = $stmtP->fetchColumn();

            $sqlA = "INSERT INTO apoderado (idPersona, ocupacion, correo, telefono) VALUES (?, ?, ?, ?)
                     RETURNING idApoderado";
            $stmtA = $obd->conexion->prepare($sqlA);
            $stmtA->execute([
                $idPersona,
                $datosApo['ocupacion'],
                $datosApo['correo'],
                $datosApo['telefono']
            ]);
            $idApoderado = $stmtA->fetchColumn();

            $obd->conexion->commit();
            return $idApoderado;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return false;
        }
    }
}
?>
