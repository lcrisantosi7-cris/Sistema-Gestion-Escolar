<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Notas/NotaController.php';
$control     = new NotaController();
$idAsignacion = $_GET['id'] ?? null;
if (!$idAsignacion) header("Location: index.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $control->guardar($_POST); }
$data        = $control->registro($idAsignacion);
$info        = $data['info'];
$competencias = $data['competencias'];
$bimestres   = $data['bimestres'];
$alumnos     = $data['alumnos'];
$notas       = $data['notas'];
?>
<style>
    .grades-header {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: var(--topbar-h);
        z-index: 50;
    }
    .grades-header-info h3 { font-size: 15px; font-weight: 700; }
    .grades-header-info p  { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }

    .student-block {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin-bottom: 12px;
        overflow: hidden;
    }
    .student-name-row {
        background: var(--gray-50);
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .student-name-row i { color: var(--color-primary); }

    .comp-row {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        gap: 16px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-100);
    }
    .comp-row:last-child { border-bottom: none; }
    .comp-text { font-size: 12px; color: var(--text-secondary); line-height: 1.4; }
    .comp-text strong { display: block; font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px; }

    .bim-inputs { display: flex; gap: 6px; }
    .bim-wrap { display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .bim-label { font-size: 9px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; }
    .grade-input {
        width: 40px;
        height: 36px;
        text-align: center;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        transition: all 0.15s;
    }
    .grade-input.editable {
        background: var(--bg-card);
        color: var(--color-primary);
        border-color: var(--color-primary);
    }
    .grade-input.editable:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .grade-input.locked {
        background: var(--gray-50);
        color: var(--text-muted);
        cursor: not-allowed;
    }
</style>

<div class="grades-header">
    <div class="grades-header-info">
        <h3><?= htmlspecialchars($info['nombreCurso']) ?></h3>
        <p><?= htmlspecialchars($info['nombreGrado']) ?> &middot; Sección <?= htmlspecialchars($info['nombreSeccion']) ?> &middot; <?= htmlspecialchars($info['nivel']) ?></p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <a href="index.php" class="btn btn-ghost">Cancelar</a>
        <button type="submit" form="formNotas" class="btn btn-success">
            <i class="fas fa-floppy-disk"></i> Guardar calificaciones
        </button>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'ok'): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Calificaciones guardadas correctamente.</div>
<?php endif; ?>

<form id="formNotas" method="POST">
    <input type="hidden" name="idAsignacion" value="<?= $idAsignacion ?>">

    <?php foreach ($alumnos as $alum): ?>
        <div class="student-block">
            <div class="student-name-row">
                <i class="fas fa-user-circle"></i>
                <?= htmlspecialchars(mb_strtoupper($alum['apellidoPaterno'] . ' ' . $alum['apellidoMaterno'] . ', ' . $alum['nombres'])) ?>
            </div>
            <?php foreach ($competencias as $comp): ?>
                <div class="comp-row">
                    <div class="comp-text">
                        <strong>Competencia</strong>
                        <?= htmlspecialchars($comp['textCompetencia']) ?>
                    </div>
                    <div class="bim-inputs">
                        <?php foreach ($bimestres as $bi):
                            $idMat  = $alum['idMatricula'];
                            $idComp = $comp['idCompetenciaCurso'];
                            $idBim  = $bi['idBimestre'];
                            $val    = $notas[$idMat][$idComp][$idBim] ?? '';
                            $active = ($bi['estado'] == 'Activo');
                        ?>
                            <div class="bim-wrap">
                                <span class="bim-label"><?= substr($bi['nombreBimestre'], 0, 3) ?></span>
                                <input type="text"
                                       name="notas[<?= $idMat ?>][<?= $idComp ?>][<?= $idBim ?>]"
                                       value="<?= htmlspecialchars($val) ?>"
                                       class="grade-input <?= $active ? 'editable' : 'locked' ?>"
                                       <?= $active ? '' : 'readonly' ?>
                                       maxlength="2"
                                       autocomplete="off">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <?php if (empty($alumnos)): ?>
        <div class="card" style="padding:0;">
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <p>No hay alumnos matriculados en esta sección</p>
            </div>
        </div>
    <?php endif; ?>
</form>

</div></main></body></html>
