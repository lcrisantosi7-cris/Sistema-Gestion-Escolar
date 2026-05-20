<?php
require_once '../../Controllers/Auth/AuthController.php';
require_once '../../Models/Administracion/Personal.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    $auth->actualizarPerfil($_POST);
}
$modPersonal = new Personal();
$misDatos    = $modPersonal->obtenerPorId($_SESSION['personal_id']);
require_once '../Layout/header.php';
$iniciales = strtoupper(substr($misDatos['nombres'], 0, 1) . substr($misDatos['apellidoPaterno'], 0, 1));
?>
<style>
    .profile-wrap {
        max-width: 640px;
        margin: 0 auto;
    }
    .profile-hero {
        background: var(--gray-900);
        border-radius: var(--radius-xl);
        padding: 32px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
        color: #fff;
    }
    .hero-avatar {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .hero-info h2 { font-size: 18px; font-weight: 700; }
    .hero-info p  { font-size: 13px; color: rgba(255,255,255,0.5); margin-top: 2px; }

    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        overflow: hidden;
    }
    .form-section {
        padding: 24px;
        border-bottom: 1px solid var(--border);
    }
    .form-section:last-child { border-bottom: none; }
    .form-section-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }

    .security-section {
        background: var(--color-warning-bg);
        border-top: 1px solid #fde68a;
    }
    .security-section .form-section-title { color: var(--color-warning); }

    .form-footer {
        padding: 20px 24px;
        background: var(--gray-50);
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>

<div class="profile-wrap">
    <div class="profile-hero">
        <div class="hero-avatar"><?= $iniciales ?></div>
        <div class="hero-info">
            <h2><?= htmlspecialchars($misDatos['nombres'] . ' ' . $misDatos['apellidoPaterno']) ?></h2>
            <p>Gestión de datos personales y credenciales</p>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'ok'): ?>
        <div class="alert alert-success" style="margin-bottom:20px;">
            <i class="fas fa-circle-check"></i> Los cambios se guardaron correctamente.
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user"></i> Datos personales</div>
                <div class="form-grid-2">
                    <div class="form-group full">
                        <label class="form-label">Nombres</label>
                        <input class="form-control" type="text" name="nombres" value="<?= htmlspecialchars($misDatos['nombres']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido paterno</label>
                        <input class="form-control" type="text" name="paterno" value="<?= htmlspecialchars($misDatos['apellidoPaterno']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido materno</label>
                        <input class="form-control" type="text" name="materno" value="<?= htmlspecialchars($misDatos['apellidoMaterno']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-section security-section">
                <div class="form-section-title"><i class="fas fa-shield-halved"></i> Seguridad</div>
                <div class="form-group">
                    <label class="form-label">Nueva contraseña</label>
                    <input class="form-control" type="password" name="password" placeholder="Dejar en blanco para mantener la actual">
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                    <i class="fas fa-circle-info"></i> Solo completa este campo si deseas cambiar tu contraseña.
                </p>
            </div>

            <div class="form-footer">
                <a href="home.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-floppy-disk"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

</div></main></body></html>
