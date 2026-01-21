<?php
ob_start();
// 1. CARGAR LAYOUT GLOBAL
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Estudiantes/DirectorioController.php';
$control = new DirectorioController();
$data = $control->index();

$periodo = $data['periodo'];
$secciones = $data['secciones'];
$estudiantes = $data['estudiantes'];
$filtro = $data['filtro'];
?>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #1d4ed8;
        --success: #10b981;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
        --bg-body: #f8fafc;
    }

    .enterprise-container {
        max-width: 1250px;
        margin: 30px auto;
        padding: 0 20px;
    }

    /* Card Principal */
    .card-ent {
        background: var(--bg-card);
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 35px;
    }

    /* Cabecera Estilo Institucional */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
    }

    .title-group h2 {
        margin: 0;
        color: var(--text-main);
        font-weight: 800;
        font-size: 1.6rem;
        letter-spacing: -0.5px;
    }

    .period-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        margin-top: 5px;
    }

    /* Barra de Herramientas y Filtros */
    .toolbar-ent {
        background: #f8fafc;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .select-ent {
        padding: 10px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        min-width: 280px;
        font-size: 14px;
        outline: none;
        transition: 0.2s;
        background: white;
    }

    .select-ent:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Tabla de Estudiantes */
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
        border-bottom: 2px solid #f1f5f9;
    }

    .table-ent td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: var(--text-main);
        vertical-align: middle;
    }

    .table-ent tr:hover td { background: #fcfdfe; }

    /* Componentes Internos */
    .student-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        background: #eff6ff;
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
    }

    .dni-label {
        font-family: 'Courier New', Courier, monospace;
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 700;
        color: #475569;
        font-size: 13px;
    }

    .badge-sec-ent {
        background: #eff6ff;
        color: var(--primary-dark);
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 12px;
    }

    /* Botones */
    .btn-ent {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        text-decoration: none;
    }

    .btn-primary-ent { background: var(--primary); color: white; }
    .btn-primary-ent:hover { background: var(--primary-dark); transform: translateY(-1px); }

    .btn-outline-ent { background: white; border: 1px solid var(--border-color); color: var(--text-muted); }
    .btn-outline-ent:hover { background: #f1f5f9; color: var(--text-main); }

    .btn-edit-ent {
        color: #f59e0b;
        background: #fffbeb;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
    }
    .btn-edit-ent:hover { background: #fef3c7; }
</style>

<div class="enterprise-container">
    <div class="card-ent">
        
        <div class="header-section">
            <div class="title-group">
                <h2><i class="fas fa-user-graduate" style="color: var(--primary); margin-right: 12px;"></i>Directorio de Estudiantes</h2>
                <div class="period-badge">
                    <i class="far fa-calendar-check"></i>
                    Periodo Académico: <?= $periodo ? $periodo['anio'] : 'No definido' ?>
                </div>
            </div>
            <a href="nueva_matricula.php" class="btn-ent btn-primary-ent">
                <i class="fas fa-plus-circle"></i> Nueva Matrícula
            </a>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg']=='editado'): ?>
            <div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #d1fae5; font-weight: 600; font-size: 14px;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i> La información de matrícula ha sido actualizada exitosamente.
            </div>
        <?php endif; ?>

        <div class="toolbar-ent">
            <form method="GET" class="filter-form">
                <div style="color: var(--text-muted); font-weight: 700; font-size: 13px; text-transform: uppercase;">
                    <i class="fas fa-filter"></i> Filtrar por Sección:
                </div>
                <select name="filtroSeccion" class="select-ent">
                    <option value="">-- Ver todos los estudiantes --</option>
                    <?php foreach($secciones as $s): ?>
                        <option value="<?= $s['idSeccion'] ?>" <?= $filtro == $s['idSeccion'] ? 'selected' : '' ?>>
                            <?= $s['nombreGrado'] . " " . $s['nivel'] . " — Sección '" . $s['nombreSeccion'] . "'" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-ent btn-outline-ent" style="padding: 10px 15px;">
                    Aplicar
                </button>
                <?php if($filtro): ?>
                    <a href="directorio.php" style="color: #ef4444; font-size: 13px; font-weight: 700; text-decoration: none; margin-left: 10px;">
                        <i class="fas fa-times-circle"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
            
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">
                Total: <span style="color: var(--text-main); font-weight: 800;"><?= count($estudiantes) ?></span> alumnos
            </div>
        </div>

        <table class="table-ent">
            <thead>
                <tr>
                    <th width="180">Identificación (DNI)</th>
                    <th>Apellidos y Nombres</th>
                    <th width="220">Grado y Sección</th>
                    <th width="180">Fecha de Registro</th>
                    <th width="120" style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($estudiantes) > 0): ?>
                    <?php foreach($estudiantes as $e): ?>
                        <tr>
                            <td>
                                <span class="dni-label"><?= $e['dni'] ?></span>
                            </td>
                            <td>
                                <div class="student-info">
                                    <div class="avatar-circle">
                                        <?= substr($e['apellidoPaterno'], 0, 1) . substr($e['nombres'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-main);"><?= $e['apellidoPaterno'] . " " . $e['apellidoMaterno'] ?></div>
                                        <div style="font-size: 13px; color: var(--primary); font-weight: 500;"><?= $e['nombres'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-sec-ent">
                                    <?= $e['nombreGrado'] . ' "' . $e['nombreSeccion'] . '"' ?>
                                </span>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 600; text-transform: uppercase;">
                                    <?= $e['nivel'] ?>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--text-muted);">
                                    <i class="far fa-calendar-alt" style="font-size: 12px;"></i>
                                    <span style="font-weight: 500;"><?= date('d M, Y', strtotime($e['fecha'])) ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="editar_matricula.php?id=<?= $e['idMatricula'] ?>" class="btn-edit-ent" title="Gestionar Matrícula">
                                    <i class="fas fa-user-edit"></i> Editar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:80px;">
                            <div style="color: #cbd5e1;">
                                <i class="fas fa-users-slash" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                                <p style="font-weight: 600; font-size: 16px;">No se encontraron registros en esta categoría.</p>
                                <p style="font-size: 14px; opacity: 0.8;">Intenta ajustar los filtros de búsqueda.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>