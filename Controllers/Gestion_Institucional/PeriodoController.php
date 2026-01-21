<?php
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';
require_once '../../Models/Gestion_Institucional/Bimestre.php';

class PeriodoController {

    private $modeloPeriodo;
    private $modeloBimestre;

    public function __construct() {
        $this->modeloPeriodo = new PeriodoAcademico();
        $this->modeloBimestre = new Bimestre();
    }
    
    private function formatearFecha($fecha) {
        if (!$fecha) return "";
        
        $timestamp = strtotime($fecha);
        $meses = [
            1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 
            5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 
            9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
        ];

        $dia = date("d", $timestamp);
        $mes = $meses[(int)date("m", $timestamp)];
        
        return $dia . " de " . $mes;
    }

    public function obtenerDatosVista() {
        $periodo = $this->modeloPeriodo->listar_Periodo_activo();
        
        $datos = [
            'anio' => '', 
            'estado' => '', 
            'textoInicio' => '', 
            'textoFin' => '', 
            'bimestres' => [] 
        ];

        if ($periodo) {
            $datos['anio'] = $periodo['anio'];
            $datos['estado'] = $periodo['estado'];
            
            $datos['textoInicio'] = $this->formatearFecha($periodo['fechaInicio']);
            $datos['textoFin'] = $this->formatearFecha($periodo['fechaFin']);
            
            $listaBimestres = $this->modeloBimestre->listarPorPeriodo($periodo['idPeriodo']);

            foreach ($listaBimestres as $bi) {
                $datos['bimestres'][] = [
                    'nombre' => $bi['nombreBimestre'],
                    'estado' => $bi['estado'],
                    'inicioTexto' => $this->formatearFecha($bi['fechaInicio']),
                    'finTexto' => $this->formatearFecha($bi['fechaFin'])
                ];
            }
        }
        return $datos;
    }

    public function Guardar($post) {
        try {
        
            $anio = $post['txtAnio'];
            $inicioPeriodo = $post['txtfecha1'];
            $finPeriodo = $post['txtfecha2'];
            
            // Fechas de bimestres
            $fechas = [
                'bi1_fin' => $post['txtFin1'],
                'bi2_ini' => $post['txtIni2'],
                'bi2_fin' => $post['txtFin2'],
                'bi3_ini' => $post['txtIni3'],
                'bi3_fin' => $post['txtFin3'],
                'bi4_ini' => $post['txtIni4']
            ];

            // 2. VALIDACIÓN LOGICA
            // ¿La clausura es después de la apertura?
            if (strtotime($finPeriodo) <= strtotime($inicioPeriodo)) {
                return "La fecha de clausura debe ser posterior a la apertura.";
            }

            // Validación de secuencia cronológica
            // El fin del Bi 1 debe ser mayor al inicio del periodo
            if (strtotime($fechas['bi1_fin']) <= strtotime($inicioPeriodo)) {
                return "El I Bimestre debe terminar después del inicio del periodo.";
            }

            // El inicio del Bi 2 debe ser mayor al fin del Bi 1, y así sucesivamente...
            if (strtotime($fechas['bi2_ini']) <= strtotime($fechas['bi1_fin'])) {
                return "El II Bimestre no puede iniciar antes de que termine el primero.";
            }
            
            if (strtotime($fechas['bi2_fin']) <= strtotime($fechas['bi2_ini'])) {
                return "El II Bimestre debe tener una duración válida (fin mayor a inicio).";
            }

            // 3. REGISTRO (Si todo es válido)
            $obj = new PeriodoAcademico($anio, $inicioPeriodo, $finPeriodo);
            $respuesta = $obj->RegistrarPeriodoYBimestres(
                $fechas['bi1_fin'],  
                $fechas['bi2_ini'], $fechas['bi2_fin'],
                $fechas['bi3_ini'], $fechas['bi3_fin'],
                $fechas['bi4_ini']   
            );

            return ($respuesta === true) ? "OK_GUARDADO" : $respuesta;

        } catch (Exception $e) {
            return "Error de sistema: " . $e->getMessage();
        }
    }

    public function verHistorial() {
        $todos = $this->modeloPeriodo->listarTodos();
        $datosHistorial = [];

        foreach ($todos as $p) {
            $p['bimestres'] = $this->modeloBimestre->listarPorPeriodo($p['idPeriodo']);
            $datosHistorial[] = $p;
        }
        return $datosHistorial;
    }

    public function Eliminar($id) {
        $res = $this->modeloPeriodo->EliminarPeriodo($id);
        if ($res === true) {
            return "OK_ELIMINADO";
        } return $res;
    }

    public function obtenerDatosCrudos() {
        $periodo = $this->modeloPeriodo->listar_Periodo_activo();
        
        $datos = [
            'idPeriodo' => '',
            'anio' => '', 
            'fechaInicio' => '',
            'fechaFin' => '', 
            'bimestres' => [] 
        ];

        if ($periodo) {
            $datos['idPeriodo'] = $periodo['idPeriodo'];
            $datos['anio'] = $periodo['anio'];
            $datos['fechaInicio'] = $periodo['fechaInicio'];
            $datos['fechaFin'] = $periodo['fechaFin'];
            
            $datos['bimestres'] = $this->modeloBimestre->listarPorPeriodo($periodo['idPeriodo']);
        }

        return $datos;
    }

    public function GuardarEdicion($post) {
        $idPeriodo = $post['idPeriodo'];
        
        $this->modeloPeriodo->ActualizarFechas($idPeriodo, $post['txtfecha1'], $post['txtfecha2']);

        #$this->modeloBimestre->ActualizarDatos($post['idBi1'], $post['txtFin1'], $post['txtfecha1'], $post['estadoBi1']);
        $this->modeloBimestre->ActualizarDatos($post['idBi1'], $post['txtfecha1'], $post['txtFin1'], $post['estadoBi1']);

        
        $this->modeloBimestre->ActualizarDatos($post['idBi2'], $post['txtIni2'], $post['txtFin2'], $post['estadoBi2']);
        
        $this->modeloBimestre->ActualizarDatos($post['idBi3'], $post['txtIni3'], $post['txtFin3'], $post['estadoBi3']);
        
        $this->modeloBimestre->ActualizarDatos($post['idBi4'], $post['txtIni4'], $post['txtfecha2'], $post['estadoBi4']);

        return "OK_EDITADO";
    }

    public function IntentarCerrarPeriodo($idPeriodo) {
        $abiertos = $this->modeloBimestre->contarBimestresAbiertos($idPeriodo);

        if ($abiertos > 0) {
            return "ERROR_BIMESTRES_ACTIVOS"; 
        } else {
            $this->modeloPeriodo->cerrarPeriodo($idPeriodo);
            return "OK_CERRADO";
        }
    }
}
