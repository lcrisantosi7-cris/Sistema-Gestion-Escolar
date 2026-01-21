<?php
// Reutilizamos el modelo existente
require_once '../../Models/Gestion_Institucional/Asignacion.php'; 
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

class MisCursosController {
    
    private $modAsignacion;
    private $modPeriodo;

    public function __construct() {
        $this->modAsignacion = new Asignacion(); // Usamos el modelo existente
        $this->modPeriodo = new PeriodoAcademico();
    }

    public function index() {
        $per = $this->modPeriodo->listar_Periodo_activo();
        $horario = [];
        
        // Estructura vacía para los 5 días
        $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];
        foreach ($dias as $dia) { $horario[$dia] = []; }

        if ($per && isset($_SESSION['personal_id'])) {
            // Llamamos a la nueva función
            $raw = $this->modAsignacion->listarHorarioDocente($_SESSION['personal_id'], $per['idPeriodo']);
            
            // Organizar el array plano en grupos por día
            foreach ($raw as $h) {
                if (isset($horario[$h['diaSemana']])) {
                    $horario[$h['diaSemana']][] = $h;
                }
            }
        }
        
        return ['periodo' => $per, 'horario' => $horario];
    }
}
?>