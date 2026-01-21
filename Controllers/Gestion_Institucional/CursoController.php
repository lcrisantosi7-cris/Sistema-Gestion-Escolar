<?php
require_once '../../Models/Gestion_Institucional/Curso.php';
require_once '../../Models/Gestion_Institucional/Nivel.php';
require_once '../../Models/Gestion_Institucional/Competencia.php';

class CursoController {

    private $modelCurso;
    private $modelNivel;
    private $modelCompetencia;

    public function __construct() {
        $this->modelCurso = new Curso();
        $this->modelNivel = new Nivel();
        $this->modelCompetencia = new Competencia();
    }

    public function index($busqueda = "") {
        return $this->modelCurso->listar($busqueda);
    }

    public function cargarNiveles() {
        return $this->modelNivel->listarTodos();
    }

    // Guardar Curso (y competencias iniciales si se envían)
    public function guardarCurso($post) {
        $idCurso = !empty($post['idCurso']) ? $post['idCurso'] : null;
        $nombre = $post['nombreCurso'];
        $idNivel = $post['idNivel'];
        
        $objCurso = new Curso($idCurso, $nombre, $idNivel);
        $idGuardado = $objCurso->guardar();

        if ($idGuardado) {
            // Si vienen competencias nuevas desde el formulario de creación
            if (isset($post['nuevasCompetencias']) && is_array($post['nuevasCompetencias'])) {
                foreach ($post['nuevasCompetencias'] as $desc) {
                    if (!empty(trim($desc))) {
                        $comp = new Competencia(null, $desc, $idGuardado);
                        $comp->guardar();
                    }
                }
            }
            return "OK";
        }
        return "Error al guardar curso";
    }

    // Para la vista de detalles
    public function verDetalles($idCurso) {
        $curso = $this->modelCurso->obtenerPorId($idCurso);
        $competencias = $this->modelCompetencia->listarPorCurso($idCurso);
        return ['curso' => $curso, 'competencias' => $competencias];
    }

    // Guardar una competencia individual (desde detalles)
    public function guardarCompetencia($post) {
        $id = !empty($post['idCompetencia']) ? $post['idCompetencia'] : null;
        $desc = $post['descripcion'];
        $idCurso = $post['idCurso'];

        $obj = new Competencia($id, $desc, $idCurso);
        $obj->guardar();
        header("Location: detalles_curso.php?id=" . $idCurso);
    }

    public function eliminarCurso($id) {
        $this->modelCurso->eliminar($id);
        header("Location: index.php?msg=eliminado");
    }

    public function eliminarCompetencia($idComp, $idCurso) {
        $this->modelCompetencia->eliminar($idComp);
        header("Location: detalles_curso.php?id=" . $idCurso);
    }
}
?>