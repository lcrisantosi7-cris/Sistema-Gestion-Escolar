<?php
ob_start();
// 1. CARGAR LAYOUT GLOBAL
require_once '../Layout/header.php';

require_once '../../Controllers/Boleta_Notas/BoletaController.php';
$control = new BoletaController();
$data = $control->index();

// Filtro seleccionado
$filtroNivel = $_GET['filtroNivel'] ?? '';
?>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #1d4ed8;
        --bg-main: #f8fafc;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
    }

    .enterprise-container {
        max-width: 1200px;
        margin: 35px auto;
        padding: 0 20px;
    }

    /* Card Principal */
    .card-ent {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 35px;
    }

    /* Header del Módulo */
    .header-boleta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--bg-main);
    }

    .header-boleta h2 {
        margin: 0;
        color: var(--text-main);
        font-weight: 800;
        font-size: 1.6rem;
        letter-spacing: -0.5px;
    }

    .period-tag {
        background: #eff6ff;
        color: var(--primary);
        padding: 5px 15px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        margin-top: 5px;
        display: inline-block;
    }

    /* Barra de Herramientas / Filtros */
    .toolbar-boletas {
        background: #f8fafc;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .select-ent {
        padding: 10px 16px;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        min-width: 260px;
        font-size: 14px;
        outline: none;
        transition: 0.2s;
        background: white;
    }

    .select-ent:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .btn-apply {
        background: var(--text-main);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-apply:hover { transform: translateY(-1px); background: #000; }

    /* Tabla Estilo Enterprise */
    .table-ent {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-ent th {
        background: #f8fafc;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 20px;
        border-bottom: 2px solid var(--bg-main);
    }

    .table-ent td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: var(--text-main);
        vertical-align: middle;
    }

    .table-ent tr:hover td { background: #fcfdfe; }

    /* Elementos de Fila */
    .badge-grade-ent {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 12px;
    }

    .dni-code {
        font-family: 'Courier New', Courier, monospace;
        color: var(--text-muted);
        font-weight: 700;
    }

    .student-name {
        color: var(--text-main);
        font-weight: 700;
        font-size: 15px;
    }

    /* Botón de Impresión */
    .btn-print-boleta {
        background: white;
        color: var(--primary);
        border: 1.5px solid var(--primary);
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }

    .btn-print-boleta:hover {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    /* Estado Vacío */
    .empty-ent {
        text-align: center;
        padding: 60px;
        color: #cbd5e1;
    }
</style>

<div class="enterprise-container">
    <div class="card-ent">
        
        <div class="header-boleta">
            <div>
                <h2><i class="fas fa-print" style="color: var(--primary); margin-right: 12px;"></i>Emisión de Boletas</h2>
                <div class="period-tag">
                    <i class="far fa-calendar-check"></i> 
                    Lectivo: <?= $data['periodo']['anio'] ?? '---' ?>
                </div>
            </div>
            <div style="text-align: right; color: var(--text-muted); font-size: 13px; font-weight: 600;">
                <i class="fas fa-user-graduate"></i> <?= count($data['estudiantes'] ?? []) ?> Alumnos registrados
            </div>
        </div>

        <form method="GET" class="toolbar-boletas">
            <span style="font-weight: 700; color: var(--text-muted); font-size: 13px; text-transform: uppercase;">
                <i class="fas fa-filter"></i> Nivel:
            </span>
            <select name="filtroNivel" class="select-ent">
                <option value="">-- Todos los niveles --</option>
                <option value="Primaria" <?= $filtroNivel == 'Primaria' ? 'selected' : '' ?>>Primaria</option>
                <option value="Secundaria" <?= $filtroNivel == 'Secundaria' ? 'selected' : '' ?>>Secundaria</option>
            </select>
            <button type="submit" class="btn-apply">Aplicar Filtro</button>
            <?php if($filtroNivel): ?>
                <a href="index.php" style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 700; margin-left: 10px;">
                    <i class="fas fa-times-circle"></i> Limpiar
                </a>
            <?php endif; ?>
        </form>

        <table class="table-ent">
            <thead>
                <tr>
                    <th width="20%">Grado / Sección</th>
                    <th width="15%">Documento DNI</th>
                    <th width="45%">Estudiante</th>
                    <th width="20%" style="text-align: center;">Documentación</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['estudiantes'])): ?>
                    <?php foreach($data['estudiantes'] as $e): ?>
                        <tr>
                            <td>
                                <span class="badge-grade-ent">
                                    <?= $e['nombreGrado'] . ' "' . $e['nombreSeccion'] . '"' ?>
                                </span>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 600; text-transform: uppercase;">
                                    <?= $e['nivel'] ?>
                                </div>
                            </td>
                            <td>
                                <span class="dni-code"><?= $e['dni'] ?></span>
                            </td>
                            <td>
                                <div class="student-name">
                                    <?= $e['apellidoPaterno']." ".$e['apellidoMaterno'].", ".$e['nombres'] ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="ver_boleta.php?id=<?= $e['idMatricula'] ?>" class="btn-print-boleta" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Generar Boleta
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-ent">
                            <i class="fas fa-search" style="font-size: 40px; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                            <p style="font-weight: 600; font-size: 16px;">No hay registros disponibles</p>
                            <p style="font-size: 14px;">Asegúrate de que existan alumnos matriculados en el nivel seleccionado.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>