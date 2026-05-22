<?php
require_once '../../Config/database.php';

class User {

    public function login($username, $password) {
        $obd = new Database();
        $obd->conectar();

        // Aliases explícitos en mayúsculas para que PDO los devuelva
        // con el mismo nombre que espera el resto del sistema,
        // independientemente de cómo PostgreSQL normalice los identificadores.
        $sql = "SELECT
                    u.idUsuario       AS \"idUsuario\",
                    u.username        AS \"username\",
                    u.password        AS \"password\",
                    u.estado          AS \"estado\",
                    p.idPersonal      AS \"idPersonal\",
                    per.nombres       AS \"nombres\",
                    per.apellidoPaterno AS \"apellidoPaterno\",
                    per.apellidoMaterno AS \"apellidoMaterno\",
                    per.dni           AS \"dni\",
                    per.idPersona     AS \"idPersona\",
                    r.nombreRol       AS \"nombreRol\"
                FROM usuario u
                INNER JOIN personal p   ON u.idPersonal = p.idPersonal
                INNER JOIN rol r        ON p.idRol      = r.idRol
                INNER JOIN persona per  ON p.idPersona  = per.idPersona
                WHERE u.username = :user
                  AND u.estado   = 'Activo'
                LIMIT 1";

        $stmt = $obd->conexion->prepare($sql);
        $stmt->bindParam(':user', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function updateProfile($idPersona, $idUsuario, $nombres, $paterno, $materno, $newPassword = null) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();

            $sqlP = "UPDATE persona
                     SET nombres = ?, apellidoPaterno = ?, apellidoMaterno = ?
                     WHERE idPersona = ?";
            $obd->conexion->prepare($sqlP)->execute([$nombres, $paterno, $materno, $idPersona]);

            if (!empty($newPassword)) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $sqlU = "UPDATE usuario SET password = ? WHERE idUsuario = ?";
                $obd->conexion->prepare($sqlU)->execute([$hash, $idUsuario]);
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
