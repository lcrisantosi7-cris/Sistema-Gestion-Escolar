<?php
// 1. Lógica de procesamiento (Siempre al principio) 🛠️
$error = '';
require_once '../../Controllers/Auth/AuthController.php';
$auth = new AuthController();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Intentamos el login. Si falla, el controlador devuelve el mensaje de error.
    // Si tiene éxito, el controlador mismo debería hacer el header("Location: ...")
    $error = $auth->login();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema Escolar</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            width: 300px; height: 300px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.2;
            top: 10%; left: 10%;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            z-index: 1;
        }

        .login-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .logo-circle {
            width: 64px;
            height: 64px;
            background: #eff6ff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary);
            font-size: 28px;
        }

        h2 { color: var(--text-main); font-weight: 800; margin: 0 0 8px 0; }
        p.subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 30px; }

        .input-group { position: relative; margin-bottom: 16px; text-align: left; }
        .input-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            box-sizing: border-box;
            background: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .error-msg {
            background: #fef2f2;
            color: #991b1b;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid #fee2e2;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer { margin-top: 30px; font-size: 11px; color: var(--text-muted); }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-circle">
                <i class="fas fa-graduation-cap"></i>
            </div>
            
            <h2>¡Bienvenido!</h2>
            <p class="subtitle">Identifícate para acceder al panel</p>
            
            <?php if(!empty($error)): ?>
                <div class="error-msg">
                    <i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Usuario" required autocomplete="off">
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
                
                <button type="submit">
                    Ingresar al Sistema <i class="fas fa-arrow-right-to-bracket"></i>
                </button>
            </form>
            
            <div class="footer">
                I.E. Mariscal Ramón Castilla Marquezado
            </div>
        </div>
    </div>

</body>
</html>