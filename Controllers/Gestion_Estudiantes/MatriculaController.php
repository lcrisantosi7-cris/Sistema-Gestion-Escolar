<?php
require_once '../../Models/Gestion_Estudiantes/Estudiante.php';
require_once '../../Models/Gestion_Estudiantes/Apoderado.php';
require_once '../../Models/Gestion_Estudiantes/Matricula.php';
require_once '../../Models/Gestion_Institucional/Seccion.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

class MatriculaController {
    
    private $modEst;
    private $modApo;
    private $modMat;
    private $modSec;
    private $modPer;

    public function __construct() {
        $this->modEst = new Estudiante();
        $this->modApo = new Apoderado();
        $this->modMat = new Matricula();
        $this->modSec = new Seccion();
        $this->modPer = new PeriodoAcademico();
    }

    public function index() {
        $datos = ['periodo'=>null, 'secciones'=>[], 'estudiante'=>null, 'apoderado'=>null, 'situacion'=>null, 'mensaje'=>'', 'tipo_mensaje'=>''];

        $datos['periodo'] = $this->modPer->listar_Periodo_activo();
        if ($datos['periodo']) {
            $datos['secciones'] = $this->modSec->listarConVacantes($datos['periodo']['idPeriodo']);
        }

        // --- PROCESAR POST (TODO EN UNO) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // BUSCAR ESTUDIANTE
            if (isset($_POST['btn_buscar_estudiante'])) {
                $dni = $_POST['dni_busqueda'] ?? '';
                $est = $this->modEst->buscarPorDni($dni);
                if ($est) {
                    $datos['estudiante'] = $est;
                    if ($est['ultimaMatricula']) {
                        $datos['situacion'] = $this->modMat->verificarSituacion($est['ultimaMatricula']);
                    } else {
                        $datos['situacion'] = ['estado' => 'SinHistorial', 'idGradoSugerido' => 0];
                    }
                } else {
                    $datos['mensaje'] = "Estudiante no encontrado."; $datos['tipo_mensaje'] = "error";
                }
                // Mantener apoderado si ya estaba
                if(!empty($_POST['dni_apo_hidden'])) $datos['apoderado'] = $this->modApo->buscarPorDni($_POST['dni_apo_hidden']);
            }

            // BUSCAR APODERADO
            if (isset($_POST['btn_buscar_apoderado'])) {
                $dni = $_POST['apo_dni_busqueda'] ?? '';
                $apo = $this->modApo->buscarPorDni($dni);
                if ($apo) {
                    $datos['apoderado'] = $apo;
                } else {
                    $datos['mensaje'] = "Apoderado no encontrado."; $datos['tipo_mensaje'] = "error";
                }
                // Mantener estudiante si ya estaba
                if(!empty($_POST['dni_est_hidden'])) {
                    $datos['estudiante'] = $this->modEst->buscarPorDni($_POST['dni_est_hidden']);
                    if($datos['estudiante']['ultimaMatricula']) 
                        $datos['situacion'] = $this->modMat->verificarSituacion($datos['estudiante']['ultimaMatricula']);
                }
            }

            // REGISTRAR MATRICULA
            if (isset($_POST['btn_registrar'])) {
                $res = $this->procesarRegistro($_POST);
                if ($res === true) {
                    header("Location: nueva_matricula.php?msg=exito");
                    exit();
                } else {
                    $datos['mensaje'] = $res; $datos['tipo_mensaje'] = "error";
                }
            }
        }
        return $datos;
    }

    private function procesarRegistro($post) {
        $idSeccion = $post['idSeccion'];
        
        // Validar Vacantes
        if (!$this->modSec->verificarCupoDisponible($idSeccion)) return "Error: Sección llena.";

        $idEstudiante = null;
        $idApoderado = null;

        // 1. REGISTRAR APODERADO
        if ($post['tipoApoderado'] == 'Nuevo') {
            $perApo = [
                'dni' => $post['apo_dni'], 
                'nombres' => $post['apo_nombres'], 
                'paterno' => $post['apo_paterno'], 
                'materno' => $post['apo_materno'], 
                'direccion' => $post['apo_direccion']
            ];
            $datApo = [
                'ocupacion' => $post['apo_ocupacion'], 
                'correo' => $post['apo_correo'], 
                'telefono' => $post['apo_telefono']
            ];
            $idApoderado = $this->modApo->registrarNuevo($perApo, $datApo);
            
            if (!$idApoderado) return "Error SQL al guardar Apoderado. Revise duplicados o campos vacíos.";
        } else {
            $idApoderado = $post['idApoderadoExistente'];
        }

        // 2. REGISTRAR ESTUDIANTE
        if ($post['tipoEstudiante'] == 'Nuevo') {
            $perEst = [
                'dni' => $post['est_dni'], 
                'nombres' => $post['est_nombres'], 
                'paterno' => $post['est_paterno'], 
                'materno' => $post['est_materno'], 
                'genero' => $post['est_genero'], 
                'nacimiento' => $post['est_nacimiento']
            ];
            $idEstudiante = $this->modEst->registrarNuevo($perEst, $idApoderado, $post['est_edad']);
            
            if (!$idEstudiante) return "Error SQL al guardar Estudiante. DNI posiblemente duplicado en tabla Persona.";
        } else {
            $idEstudiante = $post['idEstudianteExistente'];
        }

        if (!$idEstudiante || !$idApoderado) return "Error: Faltan IDs de estudiante o apoderado.";

        // 3. REGISTRAR MATRÍCULA
        $docs = [
            'ficha' => isset($post['doc_ficha']) ? 1 : 0,
            'dni' => isset($post['doc_dni']) ? 1 : 0,
            'cert' => isset($post['doc_certificado']) ? 1 : 0,
            'part' => isset($post['doc_partida']) ? 1 : 0
        ];

        return $this->modMat->registrar($idEstudiante, $post['idSeccion'], $docs) ? true : "Error BD al insertar matrícula.";
    }
}
?>