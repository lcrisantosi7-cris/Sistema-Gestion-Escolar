<?php
require_once '../../Models/Gestion_Institucional/Asignacion.php';
require_once '../../Models/Gestion_Institucional/Seccion.php';
require_once '../../Models/Gestion_Institucional/Curso.php';
require_once '../../Models/Gestion_Institucional/Personal.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';
require_once '../../Models/Gestion_Institucional/Nivel.php';

class AsignacionController {
    
    private $modelAsignacion;
    private $modelSeccion;
    private $modelCurso;
    private $modelPersonal;
    private $modelPeriodo;
    private $modelNivel;

    public function __construct() {
        $this->modelAsignacion = new Asignacion();
        $this->modelSeccion = new Seccion();
        $this->modelCurso = new Curso();
        $this->modelPersonal = new Personal();
        $this->modelPeriodo = new PeriodoAcademico();
        $this->modelNivel = new Nivel();
    }

    public function index() {
        $periodo = $this->modelPeriodo->listar_Periodo_activo();
        if (!$periodo) return [];
        return $this->modelAsignacion->listarPorPeriodo($periodo['idPeriodo']);
    }

    // Cargar datos para el formulario (Secciones, Docentes, Cursos filtrados)
    public function cargarDatosFormulario($idSeccionSeleccionada = null) {
        $periodo = $this->modelPeriodo->listar_Periodo_activo();
        if (!$periodo) die("No hay periodo activo.");

        // 1. Cargar todas las secciones del periodo
        $secciones = $this->modelSeccion->listarParaAsignacion($periodo['idPeriodo']);
        
        // 2. Cargar docentes
        $docentes = $this->modelPersonal->listarDocentes();

        // 3. Cargar Cursos (Depende de la sección seleccionada)
        $cursos = [];
        if ($idSeccionSeleccionada) {
            // Buscamos la sección seleccionada en el array que ya trajimos para ver su nivel
            foreach ($secciones as $sec) {
                if ($sec['idSeccion'] == $idSeccionSeleccionada) {
                    // $sec['nivel'] es 'Primaria' o 'Secundaria'
                    // Buscamos el ID en la tabla nivel
                    $idNivel = $this->modelNivel->obtenerIdPorNombre($sec['nivel']);
                    if ($idNivel) {
                        $cursos = $this->modelCurso->listarPorNivel($idNivel);
                    }
                    break;
                }
            }
        }

        return [
            'secciones' => $secciones,
            'docentes' => $docentes,
            'cursos' => $cursos,
            'periodo' => $periodo
        ];
    }

    public function guardar($post) {
        $id = !empty($post['idAsignacion']) ? $post['idAsignacion'] : null;
        
        $obj = new Asignacion(
            $id, 
            $post['horaInicio'], 
            $post['horaFin'], 
            $post['diaSemana'], 
            $post['idDocente'], 
            $post['idCurso'], 
            $post['idSeccion']
        );

        // 1. VALIDAR CRUCE DE DOCENTE (El profe no puede dividirse)
        if ($obj->validarCruceDocente($id)) {
            return "Error: El DOCENTE ya tiene clases asignadas en ese horario y día.";
        }

        // 2. VALIDAR CRUCE DE SECCIÓN (Los alumnos no pueden estar en dos cursos)
        if ($obj->validarCruceSeccion($id)) {
            return "Error: La SECCIÓN ya tiene otro curso programado en ese horario.";
        }

        if ($obj->guardar()) {
            header("Location: index.php?msg=guardado");
            exit;
        } else {
            return "Error al guardar en BD.";
        }
    }

    // --- NUEVO MÉTODO PARA VER HORARIO ---
    public function verHorarioSeccion($idSeccion) {
        // Obtener info de la sección para el título
        $datosSeccion = $this->modelSeccion->obtenerPorId($idSeccion); // Asumo que tienes este método en Seccion.php, si no, es fácil añadirlo
        
        // Obtener lista de cursos asignados
        $horario = $this->modelAsignacion->listarHorarioSeccion($idSeccion);
        
        return ['infoSeccion' => $datosSeccion, 'horario' => $horario];
    }

    public function eliminar($id) {
        $this->modelAsignacion->eliminar($id);
        header("Location: index.php?msg=eliminado");
    }
    
    // Método para obtener una asignación específica para editar
    public function obtenerAsignacion($id) {
        return $this->modelAsignacion->obtenerPorId($id);
    }
}
?>