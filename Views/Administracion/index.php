<?php
ob_start();
// 1. CARGAR LAYOUT GLOBAL
require_once '../Layout/header.php';

require_once '../../Controllers/Administracion/PersonalController.php';
$control = new PersonalController();

// Lógica de eliminar
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminar($_GET['id']);
}

// Cargar lista
$data = $control->index();
$lista = $data['personal'];
?>

<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-body: #f1f5f9;
        --border-color: #e2e8f0;
    }

    /* Contenedor Principal */
    .enterprise-card {
        background: white;
        padding: 35px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-color);
        margin: 20px auto;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f8fafc;
    }

    .main-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    /* Toolbar de Búsqueda y Filtros */
    .toolbar-ent {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .search-box-ent {
        display: flex;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        padding: 5px;
        border-radius: 12px;
        flex: 1;
        max-width: 550px;
        transition: 0.3s;
    }

    .search-box-ent:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        background: white;
    }

    .input-ent {
        border: none;
        background: transparent;
        padding: 10px 15px;
        width: 100%;
        outline: none;
        font-size: 14px;
        color: var(--text-main);
    }

    /* Tabla Estilizada */
    .table-enterprise {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-enterprise th {
        background: #f8fafc;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 20px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table-enterprise td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: var(--text-main);
        vertical-align: middle;
    }

    .table-enterprise tr:hover td { background: #fcfdfe; }

    /* Badges Profesionales */
    .badge-ent {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
    }
    .role-director { background: #eff6ff; color: #1d4ed8; }
    .role-secretaria { background: #f0fdf4; color: #15803d; }
    .role-docente { background: #fffbeb; color: #b45309; }

    /* Botones de Acción */
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

    .btn-add-ent { background: var(--primary); color: white; }
    .btn-add-ent:hover { background: #2563eb; transform: translateY(-2px); }

    .btn-edit-ent { background: #fefce8; color: #a16207; }
    .btn-edit-ent:hover { background: #fef9c3; }

    .btn-del-ent { background: #fef2f2; color: #b91c1c; }
    .btn-del-ent:hover { background: #fee2e2; }

    /* Estilos de contacto */
    .contact-info { font-size: 13px; color: var(--text-muted); line-height: 1.5; }
    .contact-info i { width: 18px; color: var(--primary); opacity: 0.7; }
</style>

<div class="enterprise-card">
    <div class="header-section">
        <h2 class="main-title">
            <i class="fas fa-users-cog" style="color: var(--primary);"></i>
            Gestión de Personal Institucional
        </h2>
        <a href="form_personal.php" class="btn-ent btn-add-ent">
            <i class="fas fa-plus-circle"></i> Nuevo Registro
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #d1fae5; font-weight: 600;">
            <i class="fas fa-check-circle"></i> La operación se completó exitosamente.
        </div>
    <?php endif; ?>
    
    <div class="toolbar-ent">
        <form method="POST" class="search-box-ent">
            <input type="text" name="busqueda" class="input-ent" placeholder="Buscar por DNI, Apellidos o Nombre..." value="<?= htmlspecialchars($data['busqueda'] ?? '') ?>">
            <button type="submit" class="btn-ent" style="background: var(--primary); color: white; padding: 8px 20px; border-radius: 10px;">
                <i class="fas fa-search"></i>
            </button>
            <?php if(!empty($data['busqueda'])): ?>
                <a href="index.php" class="btn-ent" style="background: transparent; color: var(--text-muted);">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="table-enterprise">
        <thead>
            <tr>
                <th width="140">Cargo / Rol</th>
                <th width="120">Documento</th>
                <th>Apellidos y Nombres</th>
                <th>Información de Contacto</th>
                <th>Acceso</th>
                <th width="110" style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if($lista): ?>
                <?php foreach($lista as $p): ?>
                    <?php 
                        $rolClass = 'role-docente';
                        if(stripos($p['nombreRol'], 'Director') !== false) $rolClass = 'role-director';
                        if(stripos($p['nombreRol'], 'Secretaria') !== false) $rolClass = 'role-secretaria';
                    ?>
                    <tr>
                        <td><span class="badge-ent <?= $rolClass ?>"><?= $p['nombreRol'] ?></span></td>
                        <td><code style="color: var(--text-main); font-weight: 600; font-size: 13px;"><?= $p['dni'] ?></code></td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);"><?= $p['apellidoPaterno']." ".$p['apellidoMaterno'] ?></div>
                            <div style="font-size: 13px; color: var(--text-muted);"><?= $p['nombres'] ?></div>
                        </td>
                        <td class="contact-info">
                            <div><i class="fas fa-envelope"></i> <?= $p['correo'] ?></div>
                            <div><i class="fas fa-phone"></i> <?= $p['telefono'] ?></div>
                        </td>
                        <td>
                            <?php if($p['username']): ?>
                                <span style="background:#f1f5f9; padding:4px 10px; border-radius:6px; font-family: 'Courier New', monospace; font-size: 12px; color: var(--primary); font-weight: 700;">
                                    @<?= $p['username'] ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #cbd5e1; font-style: italic; font-size: 12px;">Sin cuenta</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="form_personal.php?id=<?= $p['idPersonal'] ?>" class="btn-ent btn-edit-ent" style="padding: 8px 12px;" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="index.php?action=delete&id=<?= $p['idPersonal'] ?>" class="btn-ent btn-del-ent" style="padding: 8px 12px;"
                                   onclick="return confirm('¿Confirmar eliminación? Esta acción es irreversible.')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px;">
                        <i class="fas fa-search" style="font-size: 3rem; color: var(--border-color); display: block; margin-bottom: 15px;"></i>
                        <span style="color: var(--text-muted); font-weight: 500;">No se encontraron registros en la base de datos.</span>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>