<?php
require_once '../../Config/database.php';

class User {
    public function login($username, $password) {
        $obd = new Database();
        $obd->conectar();
        
        // Unimos Usuario -> Personal -> Rol -> Persona
        $sql = "SELECT u.idUsuario, u.username, u.password, u.estado,
                       p.idPersonal, per.nombres, per.apellidoPaterno, per.apellidoMaterno, per.dni, per.idPersona,
                       r.nombreRol
                FROM usuario u
                INNER JOIN personal p ON u.idPersonal = p.idPersonal
                INNER JOIN rol r ON p.idRol = r.idRol
                INNER JOIN persona per ON p.idPersona = per.idPersona
                WHERE u.username = :user AND u.estado = 'Activo'";
        
        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':user', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificamos contraseña (hash)
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Actualizar perfil (Datos personales + Password opcional)
    public function updateProfile($idPersona, $idUsuario, $nombres, $paterno, $materno, $newPassword = null) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            // 1. Actualizar Persona
            $sqlP = "UPDATE persona SET nombres=?, apellidoPaterno=?, apellidoMaterno=? WHERE idPersona=?";
            $stmtP = $obd->conexion->prepare($sqlP);
            $stmtP->execute([$nombres, $paterno, $materno, $idPersona]);

            // 2. Actualizar Password (si se envió)
            if (!empty($newPassword)) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $sqlU = "UPDATE usuario SET password=? WHERE idUsuario=?";
                $stmtU = $obd->conexion->prepare($sqlU);
                $stmtU->execute([$hash, $idUsuario]);
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