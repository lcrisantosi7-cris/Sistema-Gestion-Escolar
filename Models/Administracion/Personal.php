<?php
require_once '../../Config/database.php';

class Personal {
    
    // Listar personal (con buscador por DNI o Apellido)
    public function listar($busqueda = "") {
        $obd = new Database();
        $obd->conectar();
        
        $sql = "SELECT p.idPersonal, p.fechaContrato, p.correo, p.telefono,
                       per.dni, per.nombres, per.apellidoPaterno, per.apellidoMaterno,
                       r.nombreRol,
                       u.username, u.estado as estadoUsuario
                FROM personal p
                INNER JOIN persona per ON p.idPersona = per.idPersona
                INNER JOIN rol r ON p.idRol = r.idRol
                LEFT JOIN usuario u ON u.idPersonal = p.idPersonal";
        
        if ($busqueda != "") {
            $sql .= " WHERE per.dni LIKE :b OR per.apellidoPaterno LIKE :b";
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

    // Obtener datos completos para editar
    public function obtenerPorId($idPersonal) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT p.*, per.*, u.username, u.idUsuario 
                FROM personal p
                INNER JOIN persona per ON p.idPersona = per.idPersona
                LEFT JOIN usuario u ON u.idPersonal = p.idPersonal
                WHERE p.idPersonal = :id";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':id', $idPersonal);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // GUARDAR NUEVO (Persona -> Personal -> Usuario)
    public function registrar($datos) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            // 1. Insertar Persona
            $sqlPer = "INSERT INTO persona (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtPer = $obd->conexion->prepare($sqlPer);
            $stmtPer->execute([
                $datos['dni'], $datos['nombres'], $datos['paterno'], $datos['materno'], 
                $datos['genero'], $datos['direccion'], $datos['nacimiento']
            ]);
            $idPersona = $obd->conexion->lastInsertId();

            // 2. Insertar Personal
            $sqlPers = "INSERT INTO personal (idRol, idPersona, fechaContrato, correo, telefono) 
                        VALUES (?, ?, ?, ?, ?)";
            $stmtPers = $obd->conexion->prepare($sqlPers);
            $stmtPers->execute([
                $datos['idRol'], $idPersona, $datos['fechaContrato'], $datos['correo'], $datos['telefono']
            ]);
            $idPersonal = $obd->conexion->lastInsertId();

            // 3. Insertar Usuario (Si se proporcionó username)
            if (!empty($datos['username']) && !empty($datos['password'])) {
                // Encriptar contraseña (IMPORTANTE)
                // Usamos password_hash para seguridad, aunque tu DB dice varchar(300), cabe perfecto.
                // Si prefieres texto plano (NO RECOMENDADO), quita password_hash.
                $passHash = password_hash($datos['password'], PASSWORD_DEFAULT); 
                // Ojo: Si tu sistema usa MD5 o texto plano, ajusta aquí. Asumiré Hash seguro.
                
                $sqlUser = "INSERT INTO usuario (idPersonal, username, password, estado) VALUES (?, ?, ?, 'Activo')";
                $stmtUser = $obd->conexion->prepare($sqlUser);
                $stmtUser->execute([$idPersonal, $datos['username'], $passHash]); // Usar $passHash o $datos['password']
            }

            $obd->conexion->commit();
            return true;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    // ACTUALIZAR EXISTENTE
    public function actualizar($datos) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            // 1. Actualizar Persona
            $sqlPer = "UPDATE persona SET dni=?, nombres=?, apellidoPaterno=?, apellidoMaterno=?, genero=?, direccion=?, fechaNacimiento=? 
                       WHERE idPersona=?";
            $stmtPer = $obd->conexion->prepare($sqlPer);
            $stmtPer->execute([
                $datos['dni'], $datos['nombres'], $datos['paterno'], $datos['materno'], 
                $datos['genero'], $datos['direccion'], $datos['nacimiento'], $datos['idPersona']
            ]);

            // 2. Actualizar Personal
            $sqlPers = "UPDATE personal SET idRol=?, fechaContrato=?, correo=?, telefono=? WHERE idPersonal=?";
            $stmtPers = $obd->conexion->prepare($sqlPers);
            $stmtPers->execute([
                $datos['idRol'], $datos['fechaContrato'], $datos['correo'], $datos['telefono'], $datos['idPersonal']
            ]);

            // 3. Actualizar Usuario (Solo si existe usuario)
            if (!empty($datos['username'])) {
                // Verificar si ya tiene usuario
                $check = $obd->conexion->prepare("SELECT idUsuario FROM usuario WHERE idPersonal = ?");
                $check->execute([$datos['idPersonal']]);
                $existe = $check->fetch();

                if ($existe) {
                    // Update
                    $sqlUser = "UPDATE usuario SET username=? WHERE idPersonal=?";
                    $params = [$datos['username'], $datos['idPersonal']];
                    
                    // Si puso contraseña nueva, la actualizamos
                    if (!empty($datos['password'])) {
                        $sqlUser = "UPDATE usuario SET username=?, password=? WHERE idPersonal=?";
                        $passHash = password_hash($datos['password'], PASSWORD_DEFAULT);
                        $params = [$datos['username'], $passHash, $datos['idPersonal']];
                    }
                    $stmtUp = $obd->conexion->prepare($sqlUser);
                    $stmtUp->execute($params);
                } else {
                    // Insert (Si no tenía usuario antes)
                    if (!empty($datos['password'])) {
                        $passHash = password_hash($datos['password'], PASSWORD_DEFAULT);
                        $sqlIn = "INSERT INTO usuario (idPersonal, username, password, estado) VALUES (?, ?, ?, 'Activo')";
                        $stmtIn = $obd->conexion->prepare($sqlIn);
                        $stmtIn->execute([$datos['idPersonal'], $datos['username'], $passHash]);
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
            // Borrado en cascada manual (Usuario -> Personal. Persona se queda o se borra según lógica)
            // Aquí borraremos todo para limpiar.
            $obd->conexion->beginTransaction();
            
            // 1. Obtener idPersona para borrarla al final
            $stmtGet = $obd->conexion->prepare("SELECT idPersona FROM personal WHERE idPersonal = ?");
            $stmtGet->execute([$idPersonal]);
            $idPersona = $stmtGet->fetchColumn();

            // 2. Borrar Usuario
            $delUser = $obd->conexion->prepare("DELETE FROM usuario WHERE idPersonal = ?");
            $delUser->execute([$idPersonal]);

            // 3. Borrar Personal
            $delPers = $obd->conexion->prepare("DELETE FROM personal WHERE idPersonal = ?");
            $delPers->execute([$idPersonal]);

            // 4. Borrar Persona (Opcional, si quieres mantener historial no la borres)
            if($idPersona) {
                $delPer = $obd->conexion->prepare("DELETE FROM persona WHERE idPersona = ?");
                $delPer->execute([$idPersona]);
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