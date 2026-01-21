<?php
ob_start();
// 1. CARGAR LAYOUT GLOBAL
require_once '../Layout/header.php';

require_once '../../Controllers/Administracion/PersonalController.php';
$control = new PersonalController();

$id = $_GET['id'] ?? null;
$info = $control->form($id);
$roles = $info['roles'];
$p = $info['datos']; // Si es null, es nuevo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $control->guardar($_POST);
}
?>

<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-soft: #f8fafc;
        --border-color: #e2e8f0;
    }

    .form-enterprise-card {
        background: white;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        max-width: 900px;
        margin: 30px auto;
    }

    .form-header-ent {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--bg-soft);
    }

    .form-section-ent {
        background: white;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        position: relative;
        transition: 0.3s;
    }

    .form-section-ent:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.05);
    }

    .section-label-ent {
        position: absolute;
        top: -12px;
        left: 20px;
        background: white;
        padding: 0 12px;
        color: var(--primary);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .row-ent { display: flex; gap: 24px; margin-bottom: 20px; flex-wrap: wrap; }
    .col-ent { flex: 1; min-width: 200px; }

    .label-ent {
        display: block;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
    }

    .input-ent, .select-ent {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        background: var(--bg-soft);
        transition: 0.2s;
        box-sizing: border-box;
    }

    .input-ent:focus, .select-ent:focus {
        background: white;
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Sección de Seguridad Diferenciada */
    .section-security {
        background: #fffbeb;
        border-color: #fde68a;
    }
    .section-security .section-label-ent {
        background: #fffbeb;
        color: #d97706;
    }

    .btn-save-ent {
        width: 100%;
        padding: 16px;
        background: var(--success);
        color: white;
        border: none;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .btn-save-ent:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(16, 185, 129, 0.2);
    }

    .btn-back-ent {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }

    .btn-back-ent:hover { color: var(--text-main); }
</style>

<div class="form-enterprise-card">
    
    <div class="form-header-ent">
        <h2 style="margin:0; color: var(--text-main); font-weight: 800; letter-spacing: -0.5px;">
            <i class="fas <?= $p ? 'fa-user-edit' : 'fa-user-plus' ?>" style="color: var(--primary); margin-right: 10px;"></i>
            <?= $p ? 'Actualizar Ficha de Personal' : 'Registro de Nuevo Personal' ?>
        </h2>
        <a href="index.php" class="btn-back-ent">
            <i class="fas fa-arrow-left"></i> Volver al Directorio
        </a>
    </div>

    <form method="POST">
        <input type="hidden" name="idPersonal" value="<?= $p['idPersonal'] ?? '' ?>">
        <input type="hidden" name="idPersona" value="<?= $p['idPersona'] ?? '' ?>">

        <div class="form-section-ent">
            <span class="section-label-ent"><i class="fas fa-id-card"></i> Identidad y Básicos</span>
            
            <div class="row-ent">
                <div class="col-ent" style="flex: 0 0 180px;">
                    <label class="label-ent">Documento DNI *</label>
                    <input type="text" name="dni" value="<?= $p['dni'] ?? '' ?>" required maxlength="8" class="input-ent" placeholder="8 dígitos">
                </div>
                <div class="col-ent">
                    <label class="label-ent">Nombres Completos *</label>
                    <input type="text" name="nombres" value="<?= $p['nombres'] ?? '' ?>" required class="input-ent" placeholder="Ej: Juan Alberto">
                </div>
            </div>

            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">Apellido Paterno *</label>
                    <input type="text" name="paterno" value="<?= $p['apellidoPaterno'] ?? '' ?>" required class="input-ent">
                </div>
                <div class="col-ent">
                    <label class="label-ent">Apellido Materno *</label>
                    <input type="text" name="materno" value="<?= $p['apellidoMaterno'] ?? '' ?>" required class="input-ent">
                </div>
            </div>

            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">Fecha de Nacimiento</label>
                    <input type="date" name="nacimiento" value="<?= $p['fechaNacimiento'] ?? '' ?>" required class="input-ent">
                </div>
                <div class="col-ent">
                    <label class="label-ent">Género</label>
                    <select name="genero" class="select-ent">
                        <option value="M" <?= ($p && $p['genero']=='M')?'selected':'' ?>>Masculino</option>
                        <option value="F" <?= ($p && $p['genero']=='F')?'selected':'' ?>>Femenino</option>
                    </select>
                </div>
                <div class="col-ent" style="flex: 2;">
                    <label class="label-ent">Residencia Actual</label>
                    <input type="text" name="direccion" value="<?= $p['direccion'] ?? '' ?>" required class="input-ent" placeholder="Dirección completa">
                </div>
            </div>
        </div>

        <div class="form-section-ent">
            <span class="section-label-ent"><i class="fas fa-briefcase"></i> Relación Institucional</span>
            
            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">Rol Estratégico *</label>
                    <select name="idRol" required class="select-ent" style="border-left: 4px solid var(--primary);">
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['idRol'] ?>" <?= ($p && $p['idRol']==$r['idRol'])?'selected':'' ?>>
                                <?= $r['nombreRol'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-ent">
                    <label class="label-ent">Fecha de Incorporación</label>
                    <input type="date" name="fechaContrato" value="<?= $p['fechaContrato'] ?? date('Y-m-d') ?>" class="input-ent">
                </div>
            </div>

            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">E-mail de Contacto</label>
                    <input type="email" name="correo" value="<?= $p['correo'] ?? '' ?>" class="input-ent" placeholder="institucional@correo.com">
                </div>
                <div class="col-ent">
                    <label class="label-ent">Número Telefónico</label>
                    <input type="text" name="telefono" value="<?= $p['telefono'] ?? '' ?>" class="input-ent" placeholder="+51 999 999 999">
                </div>
            </div>
        </div>

        <div class="form-section-ent section-security">
            <span class="section-label-ent"><i class="fas fa-shield-alt"></i> Credenciales de Acceso</span>
            
            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">Nombre de Usuario</label>
                    <input type="text" name="username" value="<?= $p['username'] ?? '' ?>" class="input-ent" placeholder="Ej: jperes" style="font-family: monospace;">
                </div>
                <div class="col-ent">
                    <label class="label-ent">Contraseña <?= $p ? '<span style="color:var(--warning)">(Dejar vacío para mantener)</span>' : '*' ?></label>
                    <input type="password" name="password" <?= $p ? '' : 'required' ?> class="input-ent" placeholder="••••••••">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save-ent">
            <i class="fas fa-cloud-upload-alt"></i> SINCRONIZAR Y GUARDAR EXPEDIENTE
        </button>
    </form>
</div>