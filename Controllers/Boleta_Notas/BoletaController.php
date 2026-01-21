<?php
require_once '../../Models/Boleta_Notas/Boleta.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';
require_once '../../Models/Gestion_Institucional/Grado.php'; // Para filtros
require_once '../../Models/Gestion_Institucional/Seccion.php'; // Para filtros

class BoletaController {
    
    private $modBoleta;
    private $modPeriodo;
    private $modGrado;
    private $modSeccion;

    public function __construct() {
        $this->modBoleta = new Boleta();
        $this->modPeriodo = new PeriodoAcademico();
        $this->modGrado = new Grado();
        $this->modSeccion = new Seccion();
    }

    // Vista Principal (Tabla de alumnos)
    public function index() {
        $periodo = $this->modPeriodo->listar_Periodo_activo();
        $lista = [];
        $grados = $this->modGrado->listarTodos(); // Para llenar select filtro
        
        // Capturar filtros del GET
        $filtros = [
            'nivel' => $_GET['filtroNivel'] ?? '',
            'grado' => $_GET['filtroGrado'] ?? '',
            'seccion' => $_GET['filtroSeccion'] ?? '' // Nota: Seccion depende de logica JS o recarga, simplificado aquí
        ];

        if ($periodo) {
            $lista = $this->modBoleta->listarEstudiantes($periodo['idPeriodo'], $filtros);
        }

        return ['periodo' => $periodo, 'estudiantes' => $lista, 'grados' => $grados];
    }

    // Generar datos para la Boleta Individual
    public function generarBoleta($idMatricula) {
        $cabecera = $this->modBoleta->obtenerCabecera($idMatricula);
        
        if (!$cabecera) die("Matrícula no encontrada.");

        // Obtener cursos según si es Primaria o Secundaria
        $cursosCompetencias = $this->modBoleta->obtenerMallaCurricular($cabecera['nivel']);
        
        // Obtener Notas
        $notas = $this->modBoleta->obtenerNotas($idMatricula);
        
        // Obtener Conducta
        $conducta = $this->modBoleta->obtenerConducta($idMatricula);

        return [
            'cabecera' => $cabecera,
            'cursos' => $cursosCompetencias,
            'notas' => $notas,
            'conducta' => $conducta
        ];
    }
}
?>