<?php
require_once '../../Controllers/Boleta_Notas/BoletaController.php';
$control = new BoletaController();
if (!isset($_GET['id'])) die("ID faltante");
$idMatricula = $_GET['id'];
$data     = $control->generarBoleta($idMatricula);
$cab      = $data['cabecera'];
$cursos   = $data['cursos'];
$notas    = $data['notas'];
$conducta = $data['conducta'];
$bimestres = ['I Bimestre', 'II Bimestre', 'III Bimestre', 'IV Bimestre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta — <?= htmlspecialchars($cab['dni']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #e2e8f0;
            padding: 24px;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        /* Print button */
        .print-btn {
            position: fixed;
            top: 24px; right: 24px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
            z-index: 100;
        }
        .print-btn:hover { background: #1d4ed8; }

        /* A4 sheet */
        .sheet {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 16mm 18mm;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        }

        /* Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
            font-size: 10px;
            color: #475569;
        }
        .doc-header strong { color: #1e293b; }
        .institution-name {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 2px;
        }

        .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 18px 0 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e3a5f;
        }

        /* Student info box */
        .student-box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 11px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
        }
        .student-box .field { display: flex; gap: 4px; }
        .student-box .field strong { color: #475569; white-space: nowrap; }

        /* Grades table */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 16px;
        }
        .grades-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            color: #475569;
        }
        .grades-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .area-cell {
            font-weight: 700;
            color: #1e3a5f;
            font-size: 11px;
            background: #f8fafc;
        }
        .comp-cell {
            padding-left: 14px;
            color: #475569;
            font-style: italic;
        }
        .grade-cell {
            text-align: center;
            font-weight: 700;
        }
        .grade-fail { color: #dc2626; }
        .grade-pass { color: #1e293b; }
        .grade-avg  { background: #f8fafc; font-size: 12px; }

        /* Conduct */
        .conduct-table {
            width: 50%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 20px;
        }
        .conduct-table th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 9.5px;
            text-transform: uppercase;
            font-weight: 700;
            color: #475569;
        }
        .conduct-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: center;
            font-weight: 700;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }
        .sig-line {
            width: 180px;
            text-align: center;
        }
        .sig-line .line {
            border-top: 1.5px solid #1e293b;
            margin-bottom: 6px;
        }
        .sig-line span { font-size: 10px; font-weight: 600; color: #475569; }

        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; margin: 0; width: 100%; padding: 10mm; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <i>🖨</i> Imprimir boleta
    </button>

    <div class="sheet">
        <div class="doc-header">
            <div>
                <div><strong>DRE:</strong> PIURA &nbsp;&nbsp; <strong>UGEL:</strong> PIURA</div>
                <div class="institution-name" style="margin-top:4px;">EduCore — Institución Educativa</div>
            </div>
            <div style="text-align:right;">
                <div><strong>Nivel:</strong> <?= htmlspecialchars(strtoupper($cab['nivel'])) ?></div>
                <div><strong>Año:</strong> 2026</div>
            </div>
        </div>

        <div class="doc-title">Informe de Progreso del Aprendizaje 2026</div>

        <div class="student-box">
            <div class="field"><strong>Estudiante:</strong> <?= htmlspecialchars($cab['apePatEst'] . ' ' . $cab['apeMatEst'] . ', ' . $cab['nomEst']) ?></div>
            <div class="field"><strong>DNI:</strong> <?= htmlspecialchars($cab['dni']) ?></div>
            <div class="field"><strong>Grado y Sección:</strong> <?= htmlspecialchars($cab['nombreGrado'] . ' "' . $cab['nombreSeccion'] . '"') ?></div>
            <div class="field"><strong>Tutor(a):</strong> <?= htmlspecialchars($cab['nomTut'] . ' ' . $cab['apePatTut']) ?></div>
        </div>

        <table class="grades-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:20%; text-align:left;">Área Curricular</th>
                    <th rowspan="2" style="width:40%; text-align:left;">Competencias</th>
                    <th colspan="4">Calificativo por Bimestre</th>
                    <th rowspan="2" style="width:9%;">Prom.<br>Anual</th>
                </tr>
                <tr>
                    <th style="width:6%;">1°</th>
                    <th style="width:6%;">2°</th>
                    <th style="width:6%;">3°</th>
                    <th style="width:6%;">4°</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                    <?php $numComp = count($curso['competencias']); ?>
                    <tr>
                        <td class="area-cell" rowspan="<?= $numComp + 1 ?>"><?= htmlspecialchars(strtoupper($curso['nombreCurso'])) ?></td>
                    </tr>
                    <?php foreach ($curso['competencias'] as $comp): ?>
                        <tr>
                            <td class="comp-cell"><?= htmlspecialchars($comp['textCompetencia']) ?></td>
                            <?php
                                $suma = 0; $cnt = 0;
                                $idComp = $comp['idCompetenciaCurso'];
                            ?>
                            <?php foreach ($bimestres as $bi): ?>
                                <?php
                                    $raw = $notas[$idComp][$bi] ?? '';
                                    $val = is_numeric($raw) ? floatval($raw) : 0;
                                    $cls = ($val > 0 && $val < 11) ? 'grade-fail' : 'grade-pass';
                                    if ($raw !== '') { $suma += $val; $cnt++; }
                                ?>
                                <td class="grade-cell <?= $cls ?>"><?= $raw !== '' ? number_format($val, 0) : '—' ?></td>
                            <?php endforeach; ?>
                            <?php
                                $prom = '';
                                $promCls = 'grade-pass';
                                if ($cnt > 0) {
                                    $prom = round($suma / 4, 0, PHP_ROUND_HALF_UP);
                                    $promCls = ($prom < 11) ? 'grade-fail' : 'grade-pass';
                                }
                            ?>
                            <td class="grade-cell grade-avg <?= $promCls ?>"><?= $prom !== '' ? $prom : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="conduct-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Conducta</th>
                    <th>I</th><th>II</th><th>III</th><th>IV</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:600; font-size:10.5px;">Calificativo</td>
                    <?php foreach ($bimestres as $bi): ?>
                        <td><?= htmlspecialchars($conducta[$bi] ?? '—') ?></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-line">
                <div class="line"></div>
                <span>Firma del Tutor(a)</span>
            </div>
            <div class="sig-line">
                <div class="line"></div>
                <span>Sello de Dirección</span>
            </div>
        </div>
    </div>
</body>
</html>
