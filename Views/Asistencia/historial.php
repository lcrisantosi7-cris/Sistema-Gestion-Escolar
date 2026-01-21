<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Asistencia/AsistenciaController.php';

$control = new AsistenciaController();
$idAsignacion = $_GET['idAsignacion'] ?? null;
if(!$idAsignacion) echo "<script>window.location.href='index.php';</script>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $control->actualizar($_POST);
}

$data = $control->historial($idAsignacion);
$fechas = $data['fechas'];
$detalle = $data['detalle'];
$fechaSel = $data['fechaSel'];
?>

<style>
    /* Filtro de Búsqueda 🔍 */
    .filter-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-inline {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .select-modern {
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        min-width: 250px;
        font-size: 14px;
        background: #f8fafc;
        outline: none;
    }
    .select-modern:focus { border-color: var(--primary); }

    .btn-search {
        background: var(--sidebar-bg);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-search:hover { background: var(--primary); }

    /* Estilos de Tabla y Badges 🏷️ */
    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-asistio { background: #dcfce7; color: #166534; }
    .badge-falto { background: #fee2e2; color: #991b1b; }
    .badge-justifico { background: #fef3c7; color: #92400e; }

    .status-select-sm {
        padding: 6px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        font-weight: 500;
        width: 100%;
        background: #fff;
    }

    .btn-update {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
        transition: 0.3s;
    }
    .btn-update:hover { transform: translateY(-2px); background: #2563eb; }
</style>

<div class="section-container" style="max-width: 1000px; margin: 0 auto;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <div>
            <h2 style="margin:0; color:var(--text-main); font-weight:700;">Historial de Asistencias</h2>
            <p style="color:var(--text-muted); margin:5px 0 0 0;">Consulte y edite registros de fechas anteriores.</p>
        </div>
        <a href="index.php" style="text-decoration:none; color:var(--text-muted); font-weight:600; font-size:14px;">
            <i class="fas fa-chevron-left"></i> Volver a mis cursos
        </a>
    </div>

    <div class="filter-card">
        <label style="font-weight:700; color:var(--text-main); font-size:14px;">
            <i class="fas fa-filter" style="color:var(--primary);"></i> Buscar por fecha registrada:
        </label>
        <form method="GET" class="form-inline">
            <input type="hidden" name="idAsignacion" value="<?= $idAsignacion ?>">
            
            <select name="fecha" class="select-modern">
                <option value="">-- Seleccione una fecha --</option>
                <?php foreach($fechas as $f): ?>
                    <option value="<?= $f['fecha'] ?>" <?= $fechaSel == $f['fecha'] ? 'selected' : '' ?>>
                        📅 <?= date('d/m/Y', strtotime($f['fecha'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn-search">🔍 Ver Lista</button>
        </form>
    </div>

    <?php if($fechaSel && !empty($detalle)): ?>
        <form method="POST">
            <input type="hidden" name="idAsignacion" value="<?= $idAsignacion ?>">
            <input type="hidden" name="fechaOriginal" value="<?= $fechaSel ?>">

            <div style="background:white; border-radius:12px; border:1px solid #e2e8f0; padding:20px; margin-bottom:15px;">
                <h3 style="margin:0; font-size:16px; color:var(--text-main);">
                    <i class="fas fa-edit" style="color:var(--primary);"></i> Editando asistencia del: 
                    <span style="color:var(--primary);"><?= date('l, d \d\e F', strtotime($fechaSel)) ?></span>
                </h3>
            </div>

            <table class="table-modern" style="width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <th style="padding:15px; text-align:left; color:#64748b; font-size:12px; text-transform:uppercase;">Alumno</th>
                        <th style="padding:15px; text-align:left; color:#64748b; font-size:12px; text-transform:uppercase;">Estado Actual</th>
                        <th style="padding:15px; text-align:left; color:#64748b; font-size:12px; text-transform:uppercase; width:200px;">Modificar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detalle as $d): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:15px; font-weight:600; color:var(--text-main);">
                            <?= mb_strtoupper($d['apellidoPaterno']." ".$d['apellidoMaterno'].", ".$d['nombres']) ?>
                        </td>
                        <td style="padding:15px;">
                            <?php 
                                $badgeClass = ($d['estado']=='Asistio') ? 'badge-asistio' : (($d['estado']=='Falto') ? 'badge-falto' : 'badge-justifico');
                                $estadoTexto = ($d['estado']=='Asistio') ? 'Asistió' : (($d['estado']=='Falto') ? 'Faltó' : 'Justificó');
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $estadoTexto ?></span>
                        </td>
                        <td style="padding:15px;">
                            <select name="asistencia[<?= $d['idAsistencia'] ?>]" class="status-select-sm">
                                <option value="Asistio" <?= $d['estado']=='Asistio'?'selected':'' ?>>Asistió</option>
                                <option value="Falto" <?= $d['estado']=='Falto'?'selected':'' ?>>Faltó</option>
                                <option value="Justifico" <?= $d['estado']=='Justifico'?'selected':'' ?>>Justificó</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="text-align:right; margin-top:25px;">
                <button type="submit" class="btn-update">
                    <i class="fas fa-save"></i> ACTUALIZAR CAMBIOS
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>