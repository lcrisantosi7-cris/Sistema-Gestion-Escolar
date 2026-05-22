<?php
require_once '../../Config/database.php';

class PeriodoAcademico {
    public $idPeriodo;
    public $anio;
    public $fechaInicio;
    public $fechaFin;
    public $estado;

    public function __construct($year = null, $fechaIn = null, $fechaFi = null) {
        $this->anio       = $year;
        $this->fechaInicio = $fechaIn;
        $this->fechaFin   = $fechaFi;
    }

    public function listar_Periodo_activo() {
        $obd = new Database();
        $obd->conectar();
        $stmt = $obd->conexion->query("SELECT * FROM periodoacademico WHERE estado = 'Activo' LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function RegistrarPeriodoYBimestres($f_fin1, $f_ini2, $f_fin2, $f_ini3, $f_fin3, $f_ini4) {
        $obd = new Database();
        $obd->conectar();

        if ($this->listar_Periodo_activo()) {
            return "YA_EXISTE_ACTIVO";
        }

        try {
            // RETURNING para obtener el ID en PostgreSQL
            $sql  = "INSERT INTO periodoacademico (anio, fechaInicio, fechaFin, estado)
                     VALUES (?, ?, ?, 'Activo')
                     RETURNING idPeriodo";
            $stmt = $obd->conexion->prepare($sql);
            $stmt->execute([$this->anio, $this->fechaInicio, $this->fechaFin]);
            $idPeriodo = $stmt->fetchColumn();

            $sqlBi  = "INSERT INTO bimestre (nombreBimestre, fechaInicio, fechaFin, estado, idPeriodo) VALUES (?, ?, ?, ?, ?)";
            $stmtBi = $obd->conexion->prepare($sqlBi);

            $stmtBi->execute(['I Bimestre',   $this->fechaInicio, $f_fin1,           'Activo',   $idPeriodo]);
            $stmtBi->execute(['II Bimestre',  $f_ini2,            $f_fin2,           'Pendiente', $idPeriodo]);
            $stmtBi->execute(['III Bimestre', $f_ini3,            $f_fin3,           'Pendiente', $idPeriodo]);
            $stmtBi->execute(['IV Bimestre',  $f_ini4,            $this->fechaFin,   'Pendiente', $idPeriodo]);

            return true;
        } catch (Exception $e) {
            return "Error en el sistema: " . $e->getMessage();
        }
    }

    public function listarTodos() {
        $obd = new Database();
        $obd->conectar();
        $result = $obd->conexion->query("SELECT * FROM periodoacademico ORDER BY anio DESC");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function EliminarPeriodo($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        try {
            $obd->conexion->beginTransaction();
            $obd->conexion->prepare("DELETE FROM bimestre        WHERE idPeriodo = ?")->execute([$idPeriodo]);
            $obd->conexion->prepare("DELETE FROM periodoacademico WHERE idPeriodo = ?")->execute([$idPeriodo]);
            $obd->conexion->commit();
            return true;
        } catch (Exception $e) {
            $obd->conexion->rollBack();
            return "Error al eliminar: " . $e->getMessage();
        }
    }

    public function ActualizarFechas($id, $fInicio, $fFin) {
        $obd = new Database();
        $obd->conectar();
        $stmt = $obd->conexion->prepare("UPDATE periodoacademico SET fechaInicio = ?, fechaFin = ? WHERE idPeriodo = ?");
        return $stmt->execute([$fInicio, $fFin, $id]);
    }

    public function cerrarPeriodo($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        $stmt = $obd->conexion->prepare("UPDATE periodoacademico SET estado = 'Inactivo' WHERE idPeriodo = ?");
        return $stmt->execute([$idPeriodo]);
    }
}
?>
