<?php

class Bimestre{

    public $idBimestre;
    public $nombreBimestre;
    public $fechaInicio;
    public $fechaFin;
    public $estado;
    public $idPeriodo;

    public function __construct($nombreBi = null, $fechaIn = null, $fechaFi = null, $id = null) {
        $this->nombreBimestre = $nombreBi;
        $this->fechaInicio = $fechaIn;
        $this->fechaFin = $fechaFi;
        $this->idPeriodo = $id;
    }

    public function listarPorPeriodo($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT * FROM bimestre WHERE idPeriodo = $idPeriodo";
        $result = $obd->conexion->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ActualizarDatos($idBimestre, $fInicio, $fFin, $estado) {
        $obd = new Database();
        $obd->conectar();
        $sql = "UPDATE bimestre SET fechaInicio = ?, fechaFin = ?, estado = ? WHERE idBimestre = ?";
        $stmt = $obd->conexion->prepare($sql);
        return $stmt->execute([$fInicio, $fFin, $estado, $idBimestre]);
    }

    public function contarBimestresAbiertos($idPeriodo) {
        $obd = new Database();
        $obd->conectar();
        $sql = "SELECT COUNT(*) as total FROM bimestre WHERE idPeriodo = ? AND estado != 'Inactivo'";
        $stmt = $obd->conexion->prepare($sql);
        $stmt->execute([$idPeriodo]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila['total']; // Si devuelve > 0, es que aún hay activos o pendientes
    }

}


?>