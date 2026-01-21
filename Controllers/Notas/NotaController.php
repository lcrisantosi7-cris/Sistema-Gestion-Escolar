<?php
require_once '../../Models/Academia/Nota.php'; // Asegúrate de la ruta correcta
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

class NotaController {
    
    private $modNota;
    private $modPeriodo;

    public function __construct() {
        $this->modNota = new Nota();
        $this->modPeriodo = new PeriodoAcademico();
    }

    // Listado de Cursos
    public function index() {
        $per = $this->modPeriodo->listar_Periodo_activo();
        $cursos = [];
        if ($per && isset($_SESSION['personal_id'])) {
            $cursos = $this->modNota->listarCursosDocente($_SESSION['personal_id'], $per['idPeriodo']);
        }
        return ['periodo' => $per, 'cursos' => $cursos];
    }

    // Vista de Registro (La Matriz)
    public function registro($idAsignacion) {
        $data = $this->modNota->obtenerDatosRegistro($idAsignacion);
        if (!$data) {
            // Si no hay datos (seguridad), volver al index
            header("Location: index.php");
            exit;
        }
        return $data;
    }

    // Procesar Guardado
    public function guardar($post) {
        $idAsignacion = $post['idAsignacion'];
        
        // $post['notas'] viene como array: [idMatricula][idCompetencia][idBimestre] = valor
        if (isset($post['notas']) && is_array($post['notas'])) {
            $res = $this->modNota->guardarNotas($post['notas']);
            if ($res) {
                header("Location: registro.php?id=$idAsignacion&msg=ok");
                exit;
            } else {
                return "Error al guardar en la base de datos.";
            }
        }
        header("Location: registro.php?id=$idAsignacion"); // Si no envió nada
    }
}
?>