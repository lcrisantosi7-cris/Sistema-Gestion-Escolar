<?php
require_once '../../Config/database.php';

class Personal {

    public function listar($busqueda = "") {
        $obd = new Database();
        $obd->conectar();

        $sql = "SELECT p.idPersonal, p.fechaContrato, p.correo, p.telefono,
                       per.dni, per.nombres, per.apellidoPaterno, per.apellidoMaterno,
                       r.nombreRol,
                       u.username, u.estado as estadoUsuario
                FROM personal p
                INNER JOIN persona per ON p.idPersona = per.idPersona
                INNER JOIN rol r       ON p.idRol     = r.idRol
                LEFT  JOIN usuario u   ON u.idPersonal = p.idPersonal";

        if ($busqueda != "") {
            $sql .= " WHERE per.dni LIKE :b OR per.apellidoPaterno ILIKE :b";
        }
        $sql .= " ORDER BY per.apellidoPaterno ASC";

        $stmt = $obd->conexion->prepare($sql);
        if ($busqueda != "") {
            $param = "%" . $busqueda . "%";
            $stmt->bindParam(':b', $param);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idPersonal) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT p.*, per.*, u.username, u.idUsuario
                FROM personal p
                INNER JOIN persona per ON p.idPersona = per.idPersona
                LEFT  JOIN usuario u   ON u.idPersonal = p.idPersonal
                WHERE p.idPersonal = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idPersonal);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($datos) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            // 1. Persona — RETURNING para obtener el ID en PostgreSQL
            $sqlPer = "INSERT INTO persona (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento)
                       VALUES (?, ?, ?, ?, ?, ?, ?)
                       RETURNING idPersona";
            $stmtPer = $obd->conexion->prepare($sqlPer);
            $stmtPer->execute([
                $datos['dni'], $datos['nombres'], $datos['paterno'], $datos['materno'],
                $datos['genero'], $datos['direccion'], $datos['nacimiento']
            ]);
            $idPersona = $stmtPer->fetchColumn();

            // 2. Personal
            $sqlPers = "INSERT INTO personal (idRol, idPersona, fechaContrato, correo, telefono)
                        VALUES (?, ?, ?, ?, ?)
                        RETURNING idPersonal";
            $stmtPers = $obd->conexion->prepare($sqlPers);
            $stmtPers->execute([
                $datos['idRol'], $idPersona, $datos['fechaContrato'], $datos['correo'], $datos['telefono']
            ]);
            $idPersonal = $stmtPers->fetchColumn();

            // 3. Usuario (opcional)
            if (!empty($datos['username']) && !empty($datos['password'])) {
                $passHash = password_hash($datos['password'], PASSWORD_DEFAULT);
                $sqlUser  = "INSERT INTO usuario (idPersonal, username, password, estado) VALUES (?, ?, ?, 'Activo')";
                $obd->conexion->prepare($sqlUser)->execute([$idPersonal, $datos['username'], $passHash]);
            }

            $obd->conexion->commit();
            return true;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    public function actualizar($datos) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            $sqlPer = "UPDATE persona SET dni=?, nombres=?, apellidoPaterno=?, apellidoMaterno=?, genero=?, direccion=?, fechaNacimiento=?
                       WHERE idPersona=?";
            $obd->conexion->prepare($sqlPer)->execute([
                $datos['dni'], $datos['nombres'], $datos['paterno'], $datos['materno'],
                $datos['genero'], $datos['direccion'], $datos['nacimiento'], $datos['idPersona']
            ]);

            $sqlPers = "UPDATE personal SET idRol=?, fechaContrato=?, correo=?, telefono=? WHERE idPersonal=?";
            $obd->conexion->prepare($sqlPers)->execute([
                $datos['idRol'], $datos['fechaContrato'], $datos['correo'], $datos['telefono'], $datos['idPersonal']
            ]);

            if (!empty($datos['username'])) {
                $check = $obd->conexion->prepare("SELECT idUsuario FROM usuario WHERE idPersonal = ?");
                $check->execute([$datos['idPersonal']]);
                $existe = $check->fetch();

                if ($existe) {
                    if (!empty($datos['password'])) {
                        $passHash = password_hash($datos['password'], PASSWORD_DEFAULT);
                        $obd->conexion->prepare("UPDATE usuario SET username=?, password=? WHERE idPersonal=?")
                            ->execute([$datos['username'], $passHash, $datos['idPersonal']]);
                    } else {
                        $obd->conexion->prepare("UPDATE usuario SET username=? WHERE idPersonal=?")
                            ->execute([$datos['username'], $datos['idPersonal']]);
                    }
                } else {
                    if (!empty($datos['password'])) {
                        $passHash = password_hash($datos['password'], PASSWORD_DEFAULT);
                        $obd->conexion->prepare("INSERT INTO usuario (idPersonal, username, password, estado) VALUES (?, ?, ?, 'Activo')")
                            ->execute([$datos['idPersonal'], $datos['username'], $passHash]);
                    }
                }
            }

            $obd->conexion->commit();
            return true;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    public function eliminar($idPersonal) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            $stmtGet = $obd->conexion->prepare("SELECT idPersona FROM personal WHERE idPersonal = ?");
            $stmtGet->execute([$idPersonal]);
            $idPersona = $stmtGet->fetchColumn();

            $obd->conexion->prepare("DELETE FROM usuario  WHERE idPersonal = ?")->execute([$idPersonal]);
            $obd->conexion->prepare("DELETE FROM personal WHERE idPersonal = ?")->execute([$idPersonal]);
            if ($idPersona) {
                $obd->conexion->prepare("DELETE FROM persona WHERE idPersona = ?")->execute([$idPersona]);
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
