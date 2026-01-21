<?php
require_once '../../Models/Gestion_Estudiantes/Asistencia.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

class AsistenciaController {
    private $modAsistencia;
    private $modPeriodo;

    public function __construct() {
        $this->modAsistencia = new Asistencia();
        $this->modPeriodo = new PeriodoAcademico();
    }

    // Vista Principal: Muestra las clases asignadas al docente
    public function index() {
        $per = $this->modPeriodo->listar_Periodo_activo();
        $cargas = [];
        if ($per && isset($_SESSION['personal_id'])) {
            $cargas = $this->modAsistencia->listarCargasDocente($_SESSION['personal_id'], $per['idPeriodo']);
        }
        return ['periodo' => $per, 'cargas' => $cargas];
    }

    // Vista Registro: Muestra lista de alumnos para marcar hoy
    public function nueva($idAsignacion, $idSeccion) {
        // Validamos que la asignación pertenezca al docente (seguridad) en index o aquí
        // Por simplicidad, asumimos que viene del index correcto
        $alumnos = $this->modAsistencia->listarAlumnosPorSeccion($idSeccion);
        return $alumnos;
    }

    // Procesar Registro
    public function guardar($post) {
        $idAsignacion = $post['idAsignacion'];
        $fecha = $post['fecha']; // YYYY-MM-DD
        $asistencias = $post['asistencia']; // Array [idMatricula => Estado]

        $res = $this->modAsistencia->registrar($idAsignacion, $fecha, $asistencias);
        
        if ($res === "DUPLICADO") {
            return "Error: Ya se registró asistencia para esta fecha y curso.";
        } elseif ($res) {
            header("Location: index.php?msg=guardado");
            exit;
        } else {
            return "Error al guardar en BD.";
        }
    }

    // Vista Historial: Muestra fechas disponibles y permite filtrar
    public function historial($idAsignacion) {
        $fechas = $this->modAsistencia->listarFechasRegistradas($idAsignacion);
        
        $detalle = [];
        $fechaSeleccionada = $_GET['fecha'] ?? null;
        
        if ($fechaSeleccionada) {
            $detalle = $this->modAsistencia->obtenerDetalleFecha($idAsignacion, $fechaSeleccionada);
        }

        return ['fechas' => $fechas, 'detalle' => $detalle, 'fechaSel' => $fechaSeleccionada];
    }

    // Procesar Edición
    public function actualizar($post) {
        $idAsignacion = $post['idAsignacion'];
        $fecha = $post['fechaOriginal'];
        $cambios = $post['asistencia']; // Array [idAsistencia => NuevoEstado]

        foreach ($cambios as $idAsistencia => $estado) {
            $this->modAsistencia->actualizarEstado($idAsistencia, $estado);
        }
        
        header("Location: historial.php?idAsignacion=$idAsignacion&fecha=$fecha&msg=editado");
        exit;
    }
}
?>