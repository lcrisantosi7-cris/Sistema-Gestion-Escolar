<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Gestion_Estudiantes/MatriculaController.php';
$control   = new MatriculaController();
$data      = $control->index();
$periodo   = $data['periodo'];
$secciones = $data['secciones'];
$est       = $data['estudiante'];
$sit       = $data['situacion'];
$apo       = $data['apoderado'];
$msg       = $data['mensaje'];
$tipo      = $data['tipo_mensaje'];
$modo      = $_GET['modo'] ?? 'nuevo';
?>
<style>
    .matricula-wrap { max-width: 860px; margin: 0 auto; }

    /* Tabs */
    .tabs {
        display: flex;
        background: var(--gray-100);
        border-radius: var(--radius-md);
        padding: 4px;
        margin-bottom: 24px;
    }
    .tab-link {
        flex: 1;
        text-align: center;
        padding: 9px 12px;
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 600;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }
    .tab-link.active {
        background: var(--bg-card);
        color: var(--color-primary);
        box-shadow: var(--shadow-sm);
    }

    /* Form sections */
    .form-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 16px;
        position: relative;
    }
    .form-section-title {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--color-primary);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }

    /* Search panel */
    .search-panel {
        background: var(--gray-50);
        border: 1px dashed var(--gray-300);
        border-radius: var(--radius-md);
        padding: 16px;
        margin-bottom: 16px;
    }
    .search-panel label { font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: block; }
    .search-row { display: flex; gap: 8px; }

    /* Checkboxes */
    .check-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; margin-top: 12px; }
    .check-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.15s;
    }
    .check-item:hover { border-color: var(--color-primary); background: var(--color-primary-bg); }
    .check-item input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--color-primary); }
</style>

<div class="matricula-wrap">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-file-signature"></i> Ficha de Matrícula</div>
            <div class="page-subtitle">Año académico: <strong><?= $periodo ? $periodo['anio'] : 'Sin periodo activo' ?></strong></div>
        </div>
        <a href="directorio.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Directorio</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'exito'): ?>
        <div class="alert alert-success"><i class="fas fa-circle-check"></i> Matrícula procesada correctamente.</div>
    <?php endif; ?>
    <?php if ($msg): ?>
        <div class="alert <?= $tipo == 'ok' ? 'alert-success' : 'alert-danger' ?>">
            <i class="fas <?= $tipo == 'ok' ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <?php if (!$periodo): ?>
        <div class="alert alert-danger"><i class="fas fa-lock"></i> No hay un periodo académico activo para registrar matrículas.</div>
    <?php else: ?>

        <div class="tabs">
            <a href="?modo=nuevo" class="tab-link <?= $modo == 'nuevo' ? 'active' : '' ?>">
                <i class="fas fa-user-plus"></i> Estudiante nuevo
            </a>
            <a href="?modo=existente" class="tab-link <?= $modo == 'existente' ? 'active' : '' ?>">
                <i class="fas fa-rotate-left"></i> Estudiante existente
            </a>
        </div>

        <form method="POST">
            <input type="hidden" name="tipoEstudiante" value="<?= ucfirst($modo) ?>">
            <?php if ($est): ?><input type="hidden" name="dni_est_hidden" value="<?= htmlspecialchars($est['dni']) ?>"><?php endif; ?>
            <?php if ($apo): ?><input type="hidden" name="dni_apo_hidden" value="<?= htmlspecialchars($apo['dni']) ?>"><?php endif; ?>

            <!-- Sección 1: Estudiante -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user"></i> 1. Datos del estudiante</div>

                <?php if ($modo == 'existente'): ?>
                    <div class="search-panel">
                        <label><i class="fas fa-magnifying-glass"></i> Buscar por DNI</label>
                        <div class="search-row">
                            <input class="form-control" type="text" name="dni_busqueda" placeholder="8 dígitos" value="<?= htmlspecialchars($_POST['dni_busqueda'] ?? '') ?>" maxlength="8" style="max-width:200px;">
                            <button type="submit" name="btn_buscar_estudiante" class="btn btn-secondary">Buscar</button>
                        </div>
                    </div>
                    <?php if ($est): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-user-check"></i>
                            <strong><?= htmlspecialchars($est['nombres'] . ' ' . $est['apellidoPaterno'] . ' ' . $est['apellidoMaterno']) ?></strong>
                        </div>
                        <?php if (isset($sit['estado'])): ?>
                            <?php $sitClass = ($sit['estado'] == 'Promovido') ? 'alert-success' : (($sit['estado'] == 'Repitente') ? 'alert-danger' : 'alert-info'); ?>
                            <div class="alert <?= $sitClass ?>">
                                <i class="fas <?= $sit['estado'] == 'Promovido' ? 'fa-graduation-cap' : 'fa-rotate' ?>"></i>
                                <strong>Situación: <?= strtoupper($sit['estado']) ?></strong> — <?= $sit['estado'] == 'Promovido' ? 'Habilitado para grado superior.' : 'Debe cursar el mismo grado.' ?>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="idEstudianteExistente" value="<?= $est['idEstudiante'] ?>">
                    <?php endif; ?>
                <?php else: ?>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label">DNI *</label><input class="form-control" type="text" name="est_dni" required maxlength="8"></div>
                        <div class="form-group"><label class="form-label">Fecha de nacimiento *</label><input class="form-control" type="date" name="est_nacimiento" required></div>
                        <div class="form-group"><label class="form-label">Edad *</label><input class="form-control" type="number" name="est_edad" required></div>
                        <div class="form-group"><label class="form-label">Nombres *</label><input class="form-control" type="text" name="est_nombres" required></div>
                        <div class="form-group"><label class="form-label">Ap. paterno *</label><input class="form-control" type="text" name="est_paterno" required></div>
                        <div class="form-group"><label class="form-label">Ap. materno *</label><input class="form-control" type="text" name="est_materno" required></div>
                        <div class="form-group">
                            <label class="form-label">Género *</label>
                            <select class="form-control" name="est_genero">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sección 2: Apoderado -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user-shield"></i> 2. Datos del apoderado</div>
                <div class="search-panel">
                    <label>Vincular apoderado existente por DNI</label>
                    <div class="search-row">
                        <input class="form-control" type="text" name="apo_dni_busqueda" placeholder="DNI apoderado" value="<?= htmlspecialchars($_POST['apo_dni_busqueda'] ?? '') ?>" maxlength="8" style="max-width:200px;">
                        <button type="submit" name="btn_buscar_apoderado" class="btn btn-secondary">Cargar</button>
                    </div>
                </div>
                <input type="hidden" name="idApoderadoExistente" value="<?= $apo['idApoderado'] ?? '' ?>">
                <input type="hidden" name="tipoApoderado" value="<?= $apo ? 'Existente' : 'Nuevo' ?>">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">DNI *</label><input class="form-control" type="text" name="apo_dni" value="<?= htmlspecialchars($apo['dni'] ?? '') ?>" <?= $apo ? 'readonly' : '' ?> required maxlength="8"></div>
                    <div class="form-group"><label class="form-label">Nombres *</label><input class="form-control" type="text" name="apo_nombres" value="<?= htmlspecialchars($apo['nombres'] ?? '') ?>" <?= $apo ? 'readonly' : '' ?> required></div>
                    <div class="form-group"><label class="form-label">Ap. paterno *</label><input class="form-control" type="text" name="apo_paterno" value="<?= htmlspecialchars($apo['apellidoPaterno'] ?? '') ?>" required></div>
                    <div class="form-group"><label class="form-label">Ap. materno *</label><input class="form-control" type="text" name="apo_materno" value="<?= htmlspecialchars($apo['apellidoMaterno'] ?? '') ?>" required></div>
                    <div class="form-group"><label class="form-label">Teléfono</label><input class="form-control" type="text" name="apo_telefono" value="<?= htmlspecialchars($apo['telefono'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label">Correo</label><input class="form-control" type="email" name="apo_correo" value="<?= htmlspecialchars($apo['correo'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label">Dirección *</label><input class="form-control" type="text" name="apo_direccion" value="<?= htmlspecialchars($apo['direccion'] ?? '') ?>" required></div>
                    <div class="form-group"><label class="form-label">Ocupación</label><input class="form-control" type="text" name="apo_ocupacion" value="<?= htmlspecialchars($apo['ocupacion'] ?? '') ?>"></div>
                </div>
            </div>

            <!-- Sección 3: Ubicación -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-layer-group"></i> 3. Ubicación académica</div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Sección disponible *</label>
                    <select class="form-control" name="idSeccion" required>
                        <option value="">— Seleccionar grado y sección —</option>
                        <?php foreach ($secciones as $s): ?>
                            <?php
                                if ($modo == 'existente' && isset($sit['idGradoSugerido']) && $sit['idGradoSugerido'] > 0) {
                                    if ($s['idGrado'] != $sit['idGradoSugerido']) continue;
                                }
                                $libres = $s['vacantes'] - $s['inscritos'];
                                $dis    = ($libres <= 0) ? 'disabled' : '';
                                $txt    = ($libres <= 0) ? ' — Sin vacantes' : " — $libres vacantes";
                            ?>
                            <option value="<?= $s['idSeccion'] ?>" <?= $dis ?>>
                                <?= htmlspecialchars($s['nombreGrado'] . ' ' . $s['nivel'] . ' — Sección "' . $s['nombreSeccion'] . '"') . $txt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <label class="form-label">Documentación entregada</label>
                <div class="check-grid">
                    <label class="check-item"><input type="checkbox" name="doc_ficha" value="1"> Ficha de matrícula</label>
                    <label class="check-item"><input type="checkbox" name="doc_dni" value="1"> Copia DNI</label>
                    <?php if ($modo == 'nuevo'): ?>
                        <label class="check-item"><input type="checkbox" name="doc_certificado" value="1"> Certificado de estudios</label>
                        <label class="check-item"><input type="checkbox" name="doc_partida" value="1"> Partida de nacimiento</label>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:8px;">
                <button type="submit" name="btn_registrar" class="btn btn-primary btn-lg">
                    <i class="fas fa-floppy-disk"></i> Finalizar matrícula
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

</div></main></body></html>
