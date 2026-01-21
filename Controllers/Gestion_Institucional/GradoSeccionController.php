<?php
require_once '../../Models/Gestion_Institucional/Grado.php';
require_once '../../Models/Gestion_Institucional/Seccion.php';
require_once '../../Models/Gestion_Institucional/Personal.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

class GradoSeccionController {
    private $modelGrado;
    private $modelSeccion;
    private $modelPersonal;
    private $modelPeriodo;

    public function __construct() {
        $this->modelGrado = new Grado();
        $this->modelSeccion = new Seccion();
        $this->modelPersonal = new Personal();
        $this->modelPeriodo = new PeriodoAcademico();
    }

    public function index() {
        // 1. Obtener Periodo Activo
        $periodo = $this->modelPeriodo->listar_Periodo_activo();
        if (!$periodo) return ['error' => 'No hay periodo activo.'];

        // 2. Listar Grados
        $grados = $this->modelGrado->listarTodos();
        
        // 3. Insertar las secciones dentro de cada grado (Array anidado)
        foreach ($grados as &$g) {
            $g['secciones'] = $this->modelSeccion->listarPorGrado($g['idGrado'], $periodo['idPeriodo']);
        }

        return ['grados' => $grados, 'periodo' => $periodo];
    }

    public function formSeccion($idGrado = null, $idSeccion = null) {
        $periodo = $this->modelPeriodo->listar_Periodo_activo();
        if (!$periodo) die("No hay periodo activo");

        $docentes = $this->modelPersonal->listarDocentes();
        $seccion = null;
        
        if ($idSeccion) {
            $seccion = $this->modelSeccion->obtenerPorId($idSeccion);
        }

        return ['periodo' => $periodo, 'docentes' => $docentes, 'seccion' => $seccion, 'idGrado' => $idGrado];
    }

    public function guardarSeccion($post) {
        // Recogemos datos
        $idPeriodo = $post['idPeriodo'];
        $idGrado = $post['idGrado'];
        $nombre = strtoupper(trim($post['nombreSeccion'])); 
        $idPersonal = $post['idDocente'];
        $vacantes = $post['vacantes'];
        $idSeccion = !empty($post['idSeccion']) ? $post['idSeccion'] : null;

        // 1. INSTANCIAMOS EL OBJETO CON LOS DATOS
        // Orden del constructor: ($nombre, $vac, $idPers, $idGra, $idPer, $idSec)
        $objSeccion = new Seccion($nombre, $vacantes, $idPersonal, $idGrado, $idPeriodo, $idSeccion);

        // 2. VALIDAMOS USANDO EL OBJETO
        if ($objSeccion->validarNombreSeccion($idSeccion)) {
            return "Error: Ya existe la sección '$nombre' en este grado.";
        }

        if ($objSeccion->validarDocenteOcupado($idSeccion)) {
            return "Error: El docente seleccionado ya es tutor de otra sección.";
        }

        // 3. GUARDAMOS
        if ($objSeccion->guardar()) {
            header("Location: index.php?msg=guardado");
        } else {
            return "Error al guardar en BD.";
        }
    }

    public function eliminar($id) {
        $this->modelSeccion->eliminar($id);
        header("Location: index.php?msg=eliminado");
    }
}
?>