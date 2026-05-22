<?php
/* EduCore — Setup completo
    Ejecuta todos los pasos del README de una sola vez:
        1. Verifica la conexión a Supabase
        2. Crea todas las tablas (migration)
        3. Carga los roles, niveles y grados
        4. Crea el usuario administrador
        
        Abre: http://localhost/tu-ruta/setup.php
        
        // Elimina este archivo después de usarlo.

*/

//Seguridad básica: solo desde localhost 
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
    http_response_code(403);
    die('Acceso denegado. Este script solo puede ejecutarse desde localhost.');
}

require_once 'Config/database.php';

//  HELPERS

$log = [];   

function step(string $msg): void {
    global $log;
    $log[] = ['status' => 'step', 'msg' => $msg];
}
function ok(string $msg): void {
    global $log;
    $log[] = ['status' => 'ok', 'msg' => $msg];
}
function warn(string $msg): void {
    global $log;
    $log[] = ['status' => 'warn', 'msg' => $msg];
}
function fail(string $msg): void {
    global $log;
    $log[] = ['status' => 'error', 'msg' => $msg];
}

$hasError = false;

//  PASO 1 — Conexión

step('Verificando conexión a Supabase…');
$db = new Database();
if (!$db->conectar()) {
    fail('No se pudo conectar a Supabase. Revisa las credenciales en Config/database.php.');
    $hasError = true;
} else {
    $ver = $db->conexion->query("SELECT version()")->fetchColumn();
    ok("Conexión exitosa — $ver");
}

//  PASO 2 — Crear tablas (migration)

if (!$hasError) {
    step('Creando tablas…');

    $sqlFile = __DIR__ . '/supabase_migration.sql';
    if (!file_exists($sqlFile)) {
        fail('No se encontró supabase_migration.sql en la raíz del proyecto.');
        $hasError = true;
    } else {
        // Limpiar comentarios y dividir en sentencias
        $raw   = file_get_contents($sqlFile);
        $lines = explode("\n", $raw);
        $clean = [];
        foreach ($lines as $line) {
            $l = trim($line);
            if ($l === '' || str_starts_with($l, '--')) continue;
            $clean[] = $l;
        }
        $stmts = array_filter(array_map('trim', explode(';', implode("\n", $clean))), fn($s) => $s !== '');

        $tablesOk = 0; $tablesSkip = 0;
        foreach ($stmts as $stmt) {
            try {
                $db->conexion->exec($stmt);
                $tablesOk++;
            } catch (PDOException $e) {
                // "already exists" no es un error real
                if (str_contains($e->getMessage(), 'already exists')) {
                    $tablesSkip++;
                } else {
                    warn('SQL: ' . substr($stmt, 0, 80) . '… → ' . $e->getMessage());
                }
            }
        }
        ok("Tablas: $tablesOk sentencias ejecutadas" . ($tablesSkip > 0 ? ", $tablesSkip ya existían" : ''));
    }
}

//  PASO 3 — Roles

if (!$hasError) {
    step('Verificando roles…');
    $roles = ['Director', 'Secretaria', 'Docente'];
    $stmt  = $db->conexion->prepare(
        "INSERT INTO rol (nombreRol) SELECT ? WHERE NOT EXISTS (SELECT 1 FROM rol WHERE nombreRol = ?)"
    );
    $ins = 0;
    foreach ($roles as $r) {
        $stmt->execute([$r, $r]);
        if ($stmt->rowCount() > 0) $ins++;
    }
    ok($ins > 0 ? "Roles insertados: $ins" : 'Roles ya existían — sin cambios');
}

//  PASO 4 — Niveles

if (!$hasError) {
    step('Verificando niveles…');
    $niveles = ['Primaria', 'Secundaria'];
    $stmt    = $db->conexion->prepare(
        "INSERT INTO nivel (nombrenivel) SELECT ? WHERE NOT EXISTS (SELECT 1 FROM nivel WHERE nombrenivel = ?)"
    );
    $ins = 0;
    foreach ($niveles as $n) {
        $stmt->execute([$n, $n]);
        if ($stmt->rowCount() > 0) $ins++;
    }
    ok($ins > 0 ? "Niveles insertados: $ins" : 'Niveles ya existían — sin cambios');
}


//  PASO 5 — Grados

if (!$hasError) {
    step('Cargando grados de Primaria y Secundaria…');
    $grados = [
        ['1° Grado', 'Primaria'],  ['2° Grado', 'Primaria'],
        ['3° Grado', 'Primaria'],  ['4° Grado', 'Primaria'],
        ['5° Grado', 'Primaria'],  ['6° Grado', 'Primaria'],
        ['1° Año',   'Secundaria'],['2° Año',   'Secundaria'],
        ['3° Año',   'Secundaria'],['4° Año',   'Secundaria'],
        ['5° Año',   'Secundaria'],
    ];
    $stmt = $db->conexion->prepare(
        "INSERT INTO grado (nombreGrado, nivel)
         SELECT ?, ? WHERE NOT EXISTS (SELECT 1 FROM grado WHERE nombreGrado = ? AND nivel = ?)"
    );
    $ins = 0; $skip = 0;
    foreach ($grados as [$nombre, $nivel]) {
        $stmt->execute([$nombre, $nivel, $nombre, $nivel]);
        $stmt->rowCount() > 0 ? $ins++ : $skip++;
    }
    ok("Grados: $ins insertados" . ($skip > 0 ? ", $skip ya existían" : ''));
}


//  PASO 6 — Usuario admin

if (!$hasError) {
    step('Creando usuario administrador…');

    // ¿Ya existe?
    $check = $db->conexion->prepare("SELECT idUsuario FROM usuario WHERE username = 'admin' LIMIT 1");
    $check->execute();
    if ($check->fetch()) {
        warn("El usuario 'admin' ya existe — se omite este paso.");
    } else {
        try {
            $db->conexion->beginTransaction();

            // Rol Director
            $stmtRol = $db->conexion->prepare("SELECT idRol FROM rol WHERE nombreRol = 'Director' LIMIT 1");
            $stmtRol->execute();
            $rol   = $stmtRol->fetch();
            $idRol = $rol['idRol'] ?? $rol['idrol'];

            // Persona
            $stmtPer = $db->conexion->prepare(
                "INSERT INTO persona (dni, nombres, apellidoPaterno, apellidoMaterno, genero, direccion, fechaNacimiento)
                 VALUES ('00000000','Administrador','Sistema','EduCore','Masculino','Institución Educativa','1990-01-01')
                 RETURNING idPersona"
            );
            $stmtPer->execute();
            $idPersona = $stmtPer->fetchColumn();

            // Personal
            $stmtPers = $db->conexion->prepare(
                "INSERT INTO personal (idRol, idPersona, fechaContrato, correo, telefono)
                 VALUES (?, ?, ?, 'admin@educore.edu.pe', '000000000')
                 RETURNING idPersonal"
            );
            $stmtPers->execute([$idRol, $idPersona, date('Y-m-d')]);
            $idPersonal = $stmtPers->fetchColumn();

            // Usuario
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmtUser = $db->conexion->prepare(
                "INSERT INTO usuario (idPersonal, username, password, estado)
                 VALUES (?, 'admin', ?, 'Activo')"
            );
            $stmtUser->execute([$idPersonal, $hash]);

            $db->conexion->commit();
            ok("Usuario admin creado — usuario: <strong>admin</strong> / contraseña: <strong>admin123</strong>");
        } catch (Exception $e) {
            $db->conexion->rollBack();
            fail('Error al crear admin: ' . $e->getMessage());
            $hasError = true;
        }
    }
}


//  RESUMEN FINAL

$errors   = array_filter($log, fn($l) => $l['status'] === 'error');
$warnings = array_filter($log, fn($l) => $l['status'] === 'warn');
$allOk    = empty($errors);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCore — Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px;
            color: #0f172a;
        }
        .wrap { width: 100%; max-width: 600px; }

        /* Header */
        .header {
            background: #0f172a;
            border-radius: 16px 16px 0 0;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #fff;
        }
        .logo {
            width: 42px; height: 42px;
            background: #2563eb;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .header h1 { font-size: 18px; font-weight: 700; }
        .header p  { font-size: 13px; color: #94a3b8; margin-top: 2px; }

        /* Log */
        .log-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-top: none;
            padding: 24px 32px;
        }
        .log-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            line-height: 1.5;
        }
        .log-item:last-child { border-bottom: none; }
        .icon { font-size: 14px; margin-top: 1px; flex-shrink: 0; }
        .step  .icon { color: #94a3b8; }
        .ok    .icon { color: #16a34a; }
        .warn  .icon { color: #d97706; }
        .error .icon { color: #dc2626; }
        .step  .msg  { color: #64748b; font-weight: 500; }
        .ok    .msg  { color: #0f172a; }
        .warn  .msg  { color: #92400e; }
        .error .msg  { color: #991b1b; font-weight: 600; }

        /* Result banner */
        .result {
            border-radius: 0 0 16px 16px;
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .result.success { background: #f0fdf4; border: 1px solid #bbf7d0; border-top: none; }
        .result.failure { background: #fef2f2; border: 1px solid #fecaca; border-top: none; }
        .result-text h2 { font-size: 16px; font-weight: 700; }
        .result-text p  { font-size: 13px; margin-top: 4px; color: #64748b; }
        .result.success .result-text h2 { color: #166534; }
        .result.failure .result-text h2 { color: #991b1b; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            text-decoration: none; white-space: nowrap;
        }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-ghost   { background: #f1f5f9; color: #475569; }
        .btn-ghost:hover { background: #e2e8f0; }

        .creds {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            margin-top: 12px;
            width: 100%;
        }
        .creds strong { color: #1d4ed8; }
        code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
        }

        .warning-note {
            margin-top: 12px;
            font-size: 11px;
            color: #94a3b8;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="wrap">

    <div class="header">
        <div class="logo">🎓</div>
        <div>
            <h1>EduCore — Setup</h1>
            <p>Instalación automática del sistema</p>
        </div>
    </div>

    <div class="log-card">
        <?php foreach ($log as $item): ?>
            <div class="log-item <?= $item['status'] ?>">
                <span class="icon">
                    <?php
                        echo match($item['status']) {
                            'step'  => '›',
                            'ok'    => '✓',
                            'warn'  => '⚠',
                            'error' => '✗',
                            default => '·',
                        };
                    ?>
                </span>
                <span class="msg"><?= $item['msg'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="result <?= $allOk ? 'success' : 'failure' ?>">
        <?php if ($allOk): ?>
            <div class="result-text">
                <h2>✅ Setup completado</h2>
                <p>El sistema está listo para usarse.</p>
                <div class="creds">
                    <strong>Credenciales de acceso:</strong><br>
                    Usuario: <code>admin</code> &nbsp;·&nbsp; Contraseña: <code>admin123</code><br>
                    <small style="color:#64748b;">Cámbialas desde Perfil después del primer login.</small>
                </div>
                <p class="warning-note">!!! Elimina <code>setup.php</code> del servidor antes de publicar el proyecto.</p>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px; align-self:flex-start; margin-top:4px;">
                <a href="Views/Auth/login.php" class="btn btn-primary">→ Ir al Login</a>
                <a href="README.md" class="btn btn-ghost" target="_blank">📄 README</a>
            </div>
        <?php else: ?>
            <div class="result-text">
                <h2>❌ Setup incompleto</h2>
                <p>
                    <?= count($errors) ?> error(es) encontrado(s).
                    <?php if (!empty($warnings)): ?>
                        <?= count($warnings) ?> advertencia(s).
                    <?php endif; ?>
                    Revisa los mensajes en rojo arriba.
                </p>
                <p style="margin-top:8px; font-size:12px; color:#64748b;">
                    Causa más común: credenciales incorrectas en <code>Config/database.php</code>
                    o extensión <code>pdo_pgsql</code> deshabilitada en <code>php.ini</code>.
                </p>
            </div>
            <a href="setup.php" class="btn btn-ghost">/\ Reintentar</a>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
