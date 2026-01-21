<?php
require_once '../../Controllers/Boleta_Notas/BoletaController.php';
$control = new BoletaController();

if(!isset($_GET['id'])) die("ID faltante");
$idMatricula = $_GET['id'];

$data = $control->generarBoleta($idMatricula);
$cab = $data['cabecera'];
$cursos = $data['cursos'];
$notas = $data['notas']; 
$conducta = $data['conducta'];

$bimestres = ['I Bimestre', 'II Bimestre', 'III Bimestre', 'IV Bimestre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta_<?= $cab['dni'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --border-color: #333;
            --bg-gray: #f2f2f2;
            --text-blue: #1a3a5f;
            --text-red: #d32f2f;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: #4a4a4a; 
            margin: 0; 
            padding: 20px; 
            color: #1a1a1a;
        }
        
        /* Simulación de Hoja A4 Profesional */
        .hoja {
            background: white; 
            width: 210mm; 
            min-height: 297mm; 
            margin: 0 auto; 
            padding: 15mm 18mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.4); 
            box-sizing: border-box; 
            position: relative;
        }

        /* Encabezado Oficial */
        .header-official { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .header-official td { font-size: 11px; padding: 2px 0; color: #444; }
        
        .main-title { 
            text-align: center; 
            font-weight: 800; 
            font-size: 20px; 
            margin: 25px 0; 
            color: var(--text-blue);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Cuadro de Datos del Alumno */
        .student-info { 
            width: 100%; 
            border: 1.5px solid var(--border-color); 
            border-radius: 4px;
            padding: 12px; 
            margin-bottom: 20px;
            font-size: 12px;
            background-color: #fafafa;
        }
        .student-info td { padding: 5px; }

        /* Estructura de la Tabla de Calificaciones */
        .tabla-notas { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 11px; 
            border: 1px solid var(--border-color);
        }
        .tabla-notas th { 
            background: var(--bg-gray); 
            padding: 8px; 
            border: 1px solid var(--border-color);
            text-transform: uppercase;
            font-size: 10px;
        }
        .tabla-notas td { 
            border: 1px solid var(--border-color); 
            padding: 6px 8px; 
            vertical-align: middle;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .area-name { background: #fdfdfd; font-weight: 700; color: var(--text-blue); font-size: 11.5px; }
        .comp-text { padding-left: 15px; color: #333; font-style: italic; }

        /* Conducta */
        .conducta-box { width: 45%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        .conducta-box th { background: var(--bg-gray); padding: 5px; border: 1px solid var(--border-color); }
        .conducta-box td { border: 1px solid var(--border-color); padding: 8px; }

        /* Firmas */
        .firma-container { margin-top: 60px; width: 100%; display: flex; justify-content: space-around; }
        .firma-line { 
            width: 200px; 
            border-top: 1.5px solid #000; 
            text-align: center; 
            padding-top: 8px; 
            font-size: 11px; 
            font-weight: bold;
        }

        /* Botón de Impresión (Oculto en PDF) */
        .no-print { position: fixed; top: 30px; right: 30px; z-index: 100; }
        .btn-print { 
            padding: 12px 25px; 
            background: #2563eb; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .hoja { box-shadow: none; margin: 0; width: 100%; padding: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <span>🖨️</span> IMPRIMIR REPORTE OFICIAL
        </button>
    </div>

    <div class="hoja">
        <table class="header-official">
            <tr>
                <td width="33%"><strong>DRE:</strong> PIURA</td>
                <td width="33%" class="center"><strong>UGEL:</strong> PIURA</td>
                <td width="33%" style="text-align: right;"><strong>NIVEL:</strong> <?= strtoupper($cab['nivel']) ?></td>
            </tr>
            <tr>
                <td colspan="3" style="font-size: 13px; font-weight: bold; color: var(--text-blue);">
                    I.E. "MARISCAL RAMÓN CASTILLA MARQUEZADO"
                </td>
            </tr>
        </table>

        <div class="main-title">Informe de Progreso del Aprendizaje 2026</div>

        <table class="student-info">
            <tr>
                <td width="60%"><strong>ESTUDIANTE:</strong> <?= $cab['apePatEst']." ".$cab['apeMatEst'].", ".$cab['nomEst'] ?></td>
                <td width="40%"><strong>DNI / CÓD. MODULAR:</strong> <?= $cab['dni'] ?></td>
            </tr>
            <tr>
                <td><strong>GRADO Y SECCIÓN:</strong> <?= $cab['nombreGrado'] .' "'. $cab['nombreSeccion'] .'"' ?></td>
                <td><strong>TUTOR(A):</strong> <?= $cab['nomTut']." ".$cab['apePatTut'] ?></td>
            </tr>
        </table>

        <table class="tabla-notas">
            <thead>
                <tr>
                    <th rowspan="2" width="22%">Área Curricular</th>
                    <th rowspan="2" width="42%">Competencias Sugeridas</th>
                    <th colspan="4">Calificativo por Bimestre</th>
                    <th rowspan="2" width="10%">Prom.<br>Anual</th>
                </tr>
                <tr>
                    <th width="6%">1°</th> 
                    <th width="6%">2°</th> 
                    <th width="6%">3°</th> 
                    <th width="6%">4°</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cursos as $curso): ?>
                    <?php $numComp = count($curso['competencias']); ?>
                    <tr>
                        <td class="area-name" rowspan="<?= $numComp + 1 ?>">
                            <?= strtoupper($curso['nombreCurso']) ?>
                        </td>
                    </tr>

                    <?php foreach($curso['competencias'] as $comp): ?>
                        <tr>
                            <td class="comp-text"><?= $comp['textCompetencia'] ?></td>
                            
                            <?php 
                                $suma = 0; $contador = 0;
                                $idComp = $comp['idCompetenciaCurso'];
                            ?>

                            <?php foreach($bimestres as $bi): ?>
                                <?php 
                                    $notaRaw = $notas[$idComp][$bi] ?? '';
                                    $val = is_numeric($notaRaw) ? floatval($notaRaw) : 0;
                                    $color = ($val > 0 && $val < 11) ? 'var(--text-red)' : '#111';
                                    
                                    if ($notaRaw !== '') {
                                        $suma += $val;
                                        $contador++; 
                                    }
                                ?>
                                <td class="center bold" style="color: <?= $color ?>">
                                    <?= $notaRaw !== '' ? number_format($val, 0) : '-' ?>
                                </td>
                            <?php endforeach; ?>

                            <?php 
                                // Lógica de Promedio (Suma de los 4 bimestres / 4)
                                $promFinal = '';
                                if ($contador > 0) {
                                    $calc = $suma / 4; 
                                    $promFinal = round($calc, 0, PHP_ROUND_HALF_UP);
                                    $colorProm = ($promFinal < 11) ? 'var(--text-red)' : 'var(--text-blue)';
                                }
                            ?>
                            <td class="center bold" style="color: <?= $colorProm ?>; background: #f9f9f9; font-size: 13px;">
                                <?= $promFinal ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="conducta-box">
            <thead>
                <tr>
                    <th>EVALUACIÓN DE CONDUCTA</th>
                    <th>I</th> <th>II</th> <th>III</th> <th>IV</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center"><strong>Calificativo Literal</strong></td>
                    <?php foreach($bimestres as $bi): ?>
                        <td class="center bold" style="font-size: 13px;"><?= $conducta[$bi] ?? '-' ?></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>

        <div class="firma-container">
            <div class="firma-line">Firma del Tutor(a)</div>
            <div class="firma-line">Sello de Dirección</div>
        </div>

    </div>
</body>
</html>