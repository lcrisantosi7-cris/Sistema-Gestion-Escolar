<?php
require_once '../../Config/database.php';

class Personal {
    public $idPersonal;
    public $nombres;
    public $apellidoPaterno;
    public $apellidoMaterno;
    // ... otros atributos si quisieras

    public function __construct($id = null, $nom = null, $apeP = null, $apeM = null) {
        $this->idPersonal = $id;
        $this->nombres = $nom;
        $this->apellidoPaterno = $apeP;
        $this->apellidoMaterno = $apeM;
    }

    public function listarDocentes() {
        $obd = new Database();
        $obd->conectar();
        
        // CORRECCIÓN: Unimos Personal con Persona y Rol
        $sql = "SELECT p.idPersonal, per.nombres, per.apellidoPaterno, per.apellidoMaterno 
                FROM personal p 
                INNER JOIN persona per ON p.idPersona = per.idPersona
                INNER JOIN rol r ON p.idRol = r.idRol 
                WHERE r.nombreRol = 'Docente'"; // Asegúrate que 'Docente' es el nombre exacto en tu BD
                
        $result = $obd->conexion->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>