<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Administracion/PersonalController.php';
$control = new PersonalController();
$id   = $_GET['id'] ?? null;
$info = $control->form($id);
$roles = $info['roles'];
$p     = $info['datos'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $control->guardar($_POST); }
$isEdit = !empty($p);
?>
<style>
    .form-wrap { max-width: 780px; margin: 0 auto; }
    .form-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; }
    .form-section { padding: 24px; border-bottom: 1px solid var(--border); }
    .form-section:last-of-type { border-bottom: none; }
    .form-section-title {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--text-muted);
        margin-bottom: 18px; display: flex; align-items: center; gap: 7px;
    }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }
    .form-group.third { grid-column: span 1; }
    .security-section { background: var(--color-warning-bg); }
    .security-section .form-section-title { color: var(--color-warning); }
    .form-footer {
        padding: 20px 24px; background: var(--gray-50);
        border-top: 1px solid var(--border);
        display: flex; justify-content: space-between; align-items: center;
    }
</style>

<div class="form-wrap">
    <div class="page-header" style="margin-bottom:20px;">
        <div>
            <div class="page-title">
                <i class="fas <?= $isEdit ? 'fa-user-pen' : 'fa-user-plus' ?>"></i>
                <?= $isEdit ? 'Editar registro' : 'Nuevo personal' ?>
            </div>
            <div class="page-subtitle"><?= $isEdit ? 'Actualiza los datos del colaborador' : 'Completa el formulario para registrar un nuevo colaborador' ?></div>
        </div>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div class="form-card">
        <form method="POST">
            <input type="hidden" name="idPersonal" value="<?= $p['idPersonal'] ?? '' ?>">
            <input type="hidden" name="idPersona"  value="<?= $p['idPersona']  ?? '' ?>">

            <!-- Identidad -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-id-card"></i> Identidad</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">DNI *</label>
                        <input class="form-control" type="text" name="dni" value="<?= htmlspecialchars($p['dni'] ?? '') ?>" required maxlength="8" placeholder="8 dígitos">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nombres *</label>
                        <input class="form-control" type="text" name="nombres" value="<?= htmlspecialchars($p['nombres'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido paterno *</label>
                        <input class="form-control" type="text" name="paterno" value="<?= htmlspecialchars($p['apellidoPaterno'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido materno *</label>
                        <input class="form-control" type="text" name="materno" value="<?= htmlspecialchars($p['apellidoMaterno'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de nacimiento</label>
                        <input class="form-control" type="date" name="nacimiento" value="<?= $p['fechaNacimiento'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Género</label>
                        <select class="form-control" name="genero">
                            <option value="M" <?= ($p && $p['genero']=='M') ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= ($p && $p['genero']=='F') ? 'selected' : '' ?>>Femenino</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Dirección</label>
                        <input class="form-control" type="text" name="direccion" value="<?= htmlspecialchars($p['direccion'] ?? '') ?>" required>
                    </div>
                </div>
            </div>

            <!-- Relación institucional -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-briefcase"></i> Relación institucional</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Rol *</label>
                        <select class="form-control" name="idRol" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['idRol'] ?>" <?= ($p && $p['idRol']==$r['idRol']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nombreRol']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de incorporación</label>
                        <input class="form-control" type="date" name="fechaContrato" value="<?= $p['fechaContrato'] ?? date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <input class="form-control" type="email" name="correo" value="<?= htmlspecialchars($p['correo'] ?? '') ?>" placeholder="correo@institución.edu">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input class="form-control" type="text" name="telefono" value="<?= htmlspecialchars($p['telefono'] ?? '') ?>" placeholder="+51 999 999 999">
                    </div>
                </div>
            </div>

            <!-- Credenciales -->
            <div class="form-section security-section">
                <div class="form-section-title"><i class="fas fa-shield-halved"></i> Credenciales de acceso</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nombre de usuario</label>
                        <input class="form-control font-mono" type="text" name="username" value="<?= htmlspecialchars($p['username'] ?? '') ?>" placeholder="ej: jperez">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Contraseña <?= $isEdit ? '<span style="font-weight:400; color:var(--text-muted);">(vacío = sin cambios)</span>' : '*' ?>
                        </label>
                        <input class="form-control" type="password" name="password" <?= $isEdit ? '' : 'required' ?> placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="index.php" class="btn btn-ghost"><i class="fas fa-xmark"></i> Cancelar</a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-floppy-disk"></i> <?= $isEdit ? 'Guardar cambios' : 'Registrar personal' ?>
                </button>
            </div>
        </form>
    </div>
</div>

</div></main></body></html>
