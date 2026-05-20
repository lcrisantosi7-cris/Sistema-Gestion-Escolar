<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Gestion_Estudiantes/DirectorioController.php';
$control = new DirectorioController();
if (!isset($_GET['id'])) { echo "<script>window.location.href='directorio.php';</script>"; exit(); }
$idMatricula = $_GET['id'];
$data        = $control->editar($idMatricula);
$mat         = $data['matricula'];
$secciones   = $data['secciones'];
$mensaje     = $data['mensaje'];
?>
<style>
    .edit-wrap { max-width: 600px; margin: 0 auto; }
    .form-section { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 16px; }
    .form-section-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 16px; }
    .check-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px; }
    .check-item { display: flex; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer; font-size: 13px; font-weight: 500; }
    .check-item:hover { border-color: var(--color-primary); background: var(--color-primary-bg); }
    .check-item input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--color-primary); }
    .form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
</style>

<div class="edit-wrap">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-user-pen"></i> Editar Matrícula</div>
            <div class="page-subtitle"><?= htmlspecialchars($mat['nombres'] . ' ' . $mat['apellidoPaterno'] . ' ' . $mat['apellidoMaterno']) ?></div>
        </div>
        <a href="directorio.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-section">
            <div class="form-section-title">Reasignar sección</div>
            <div class="form-group">
                <label class="form-label">Sección *</label>
                <select class="form-control" name="idSeccion" required>
                    <?php foreach ($secciones as $s): ?>
                        <?php
                            $libre    = $s['vacantes'] - $s['inscritos'];
                            $esActual = ($s['idSeccion'] == $mat['idSeccion']);
                            if ($esActual) $libre++;
                            $dis  = ($libre <= 0 && !$esActual) ? 'disabled' : '';
                            $txt  = $esActual ? ' (actual)' : " — $libre vacantes";
                        ?>
                        <option value="<?= $s['idSeccion'] ?>" <?= $esActual ? 'selected' : '' ?> <?= $dis ?>>
                            <?= htmlspecialchars($s['nombreGrado'] . ' ' . $s['nivel'] . ' — "' . $s['nombreSeccion'] . '"') . $txt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Documentación</div>
            <div class="check-grid">
                <label class="check-item"><input type="checkbox" name="doc_ficha" value="1" <?= $mat['doc_ficha_matricula'] ? 'checked' : '' ?>> Ficha de matrícula</label>
                <label class="check-item"><input type="checkbox" name="doc_dni" value="1" <?= $mat['doc_copia_dni'] ? 'checked' : '' ?>> Copia DNI</label>
                <label class="check-item"><input type="checkbox" name="doc_certificado" value="1" <?= $mat['doc_certificado_estudios'] ? 'checked' : '' ?>> Certificado de estudios</label>
                <label class="check-item"><input type="checkbox" name="doc_partida" value="1" <?= $mat['doc_partida_nacimiento'] ? 'checked' : '' ?>> Partida de nacimiento</label>
            </div>
        </div>

        <div class="form-footer">
            <a href="directorio.php" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-floppy-disk"></i> Guardar cambios</button>
        </div>
    </form>
</div>

</div></main></body></html>
