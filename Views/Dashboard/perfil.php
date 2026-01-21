<?php
// 1. LÓGICA DE NEGOCIO (Intacta)
require_once '../../Controllers/Auth/AuthController.php';
require_once '../../Models/Administracion/Personal.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    $auth->actualizarPerfil($_POST); 
}

$modPersonal = new Personal();
$misDatos = $modPersonal->obtenerPorId($_SESSION['personal_id']);

// 2. CARGAR LAYOUT
require_once '../Layout/header.php'; 

$iniciales = strtoupper(substr($misDatos['nombres'], 0, 1) . substr($misDatos['apellidoPaterno'], 0, 1));
?>

<style>
    /* CONTENEDOR PRINCIPAL */
    .profile-container {
        display: flex;
        justify-content: center;
        padding-top: 30px;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .profile-card {
        background: white;
        width: 100%;
        max-width: 800px;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    /* ENCABEZADO "ENTERPRISE" */
    .profile-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 50px 20px 40px;
        text-align: center;
        color: white;
        position: relative;
    }

    .avatar-circle {
        width: 110px;
        height: 110px;
        background: white;
        color: #1e293b;
        border-radius: 35px; /* Estilo Squircle */
        font-size: 40px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        border: 5px solid rgba(255,255,255,0.1);
    }

    .profile-header h2 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
    .profile-header p { margin: 8px 0 0; font-size: 15px; opacity: 0.7; font-weight: 300; }

    .profile-body { padding: 45px; }

    /* FORMULARIO */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .form-group.full-width { grid-column: span 2; }

    label {
        display: block;
        margin-bottom: 10px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 14px 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 15px;
        color: #1e293b;
        transition: all 0.3s;
        background: #f8fafc;
        box-sizing: border-box;
    }

    input:focus {
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
        outline: none;
    }

    /* SECCIÓN DE SEGURIDAD */
    .security-section {
        background: #fdfaf3;
        border: 1px solid #fef3c7;
        padding: 25px;
        border-radius: 16px;
        margin-top: 10px;
    }
    
    .security-title {
        color: #92400e;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* BOTÓN DE ACCIÓN */
    .btn-update {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 16px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        width: 100%;
        margin-top: 30px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .btn-update:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
    }

    /* ALERTAS SUTILES */
    .success-badge {
        background: #ecfdf5;
        color: #065f46;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 30px;
        border: 1px solid #d1fae5;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
</style>

<div class="profile-container">
    <div class="profile-card">
        
        <div class="profile-header">
            <div class="avatar-circle">
                <?= $iniciales ?>
            </div>
            <h2><?= $misDatos['nombres'] . ' ' . $misDatos['apellidoPaterno'] ?></h2>
            <p>Gestión de credenciales y datos personales del usuario</p>
        </div>

        <div class="profile-body">
            
            <?php if(isset($_GET['msg']) && $_GET['msg']=='ok'): ?>
                <div class="success-badge">
                    <i class="fas fa-check-circle" style="font-size: 18px;"></i> 
                    Los cambios se han guardado correctamente en el sistema.
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label><i class="fas fa-user-edit" style="margin-right: 5px;"></i> Nombres Completos</label>
                        <input type="text" name="nombres" value="<?= $misDatos['nombres'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Apellido Paterno</label>
                        <input type="text" name="paterno" value="<?= $misDatos['apellidoPaterno'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Apellido Materno</label>
                        <input type="text" name="materno" value="<?= $misDatos['apellidoMaterno'] ?>" required>
                    </div>
                </div>

                <div class="security-section">
                    <div class="security-title">
                        <i class="fas fa-shield-halved"></i> Seguridad de Acceso
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Nueva Contraseña de Ingreso</label>
                        <input type="password" name="password" placeholder="••••••••••••">
                        <small style="color: #94a3b8; display: block; margin-top: 10px; font-size: 12px;">
                            <i class="fas fa-circle-info" style="margin-right: 4px;"></i> 
                            Dejar en blanco si desea conservar su contraseña actual.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn-update">
                    <i class="fas fa-cloud-arrow-up"></i> Actualizar Información
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>