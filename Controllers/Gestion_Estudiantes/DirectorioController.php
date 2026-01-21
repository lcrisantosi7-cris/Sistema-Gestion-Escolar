<?php
require_once '../../Models/Gestion_Estudiantes/Matricula.php';
require_once '../../Models/Gestion_Institucional/Seccion.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

class DirectorioController {
    
    private $modMat;
    private $modSec;
    private $modPer;

    public function __construct() {
        $this->modMat = new Matricula();
        $this->modSec = new Seccion();
        $this->modPer = new PeriodoAcademico();
    }

    // --- LISTADO GENERAL ---
    public function index() {
        $datos = ['periodo'=>null, 'secciones'=>[], 'estudiantes'=>[], 'filtro'=>null];

        // 1. Periodo Activo
        $periodo = $this->modPer->listar_Periodo_activo();
        $datos['periodo'] = $periodo;

        if ($periodo) {
            // 2. Cargar Secciones para el filtro
            $datos['secciones'] = $this->modSec->listarConVacantes($periodo['idPeriodo']);
            
            // 3. Aplicar Filtro si existe
            $idSeccionFiltro = isset($_GET['filtroSeccion']) && $_GET['filtroSeccion'] != "" ? $_GET['filtroSeccion'] : null;
            $datos['filtro'] = $idSeccionFiltro;

            // 4. Buscar Estudiantes
            $datos['estudiantes'] = $this->modMat->listarMatriculados($periodo['idPeriodo'], $idSeccionFiltro);
        }

        return $datos;
    }

    // --- EDICIÓN DE MATRÍCULA ---
    public function editar($idMatricula) {
        $datos = ['matricula'=>null, 'secciones'=>[], 'mensaje'=>''];

        // Cargar datos de la matrícula
        $datos['matricula'] = $this->modMat->obtenerPorId($idMatricula);
        
        // Cargar secciones para el desplegable (del periodo activo)
        $periodo = $this->modPer->listar_Periodo_activo();
        if ($periodo) {
            $datos['secciones'] = $this->modSec->listarConVacantes($periodo['idPeriodo']);
        }

        // Procesar cambios
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idSeccionNueva = $_POST['idSeccion'];
            $idSeccionActual = $datos['matricula']['idSeccion'];

            // Validar vacantes SOLO si cambió de sección
            if ($idSeccionNueva != $idSeccionActual) {
                if (!$this->modSec->verificarCupoDisponible($idSeccionNueva)) {
                    $datos['mensaje'] = "Error: La nueva sección seleccionada no tiene vacantes.";
                    return $datos;
                }
            }

            $docs = [
                'ficha' => isset($_POST['doc_ficha']) ? 1 : 0,
                'dni' => isset($_POST['doc_dni']) ? 1 : 0,
                'cert' => isset($_POST['doc_certificado']) ? 1 : 0,
                'part' => isset($_POST['doc_partida']) ? 1 : 0
            ];

            if ($this->modMat->actualizar($idMatricula, $idSeccionNueva, $docs)) {
                header("Location: directorio.php?msg=editado");
                exit();
            } else {
                $datos['mensaje'] = "Error al actualizar en BD.";
            }
        }

        return $datos;
    }
}
?>