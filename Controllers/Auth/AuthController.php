<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Models/Auth/User.php';

class AuthController {
    private $model;

    public function __construct() {
        $this->model = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['username'];
            $pass = $_POST['password'];

            $userData = $this->model->login($user, $pass);
            $db = (new Database())->conectar();
$this->model = new User($db);


            if ($userData) {
                // MODIFICACIÓN: Agregamos 'Docente' al array de roles permitidos
                if (in_array($userData['nombreRol'], ['Director', 'Secretaria', 'Docente'])) {
                    session_regenerate_id(true);
                    
                    $_SESSION['user_id'] = $userData['idUsuario'];
                    $_SESSION['personal_id'] = $userData['idPersonal'];
                    $_SESSION['persona_id'] = $userData['idPersona'];
                    $_SESSION['nombre_completo'] = $userData['nombres'] . ' ' . $userData['apellidoPaterno'];
                    $_SESSION['rol'] = $userData['nombreRol'];
                    
                    header("Location: ../Dashboard/home.php");
                    exit();
                } else {
                    return "Acceso restringido. Rol no autorizado.";
                }
            } else {
                return "Credenciales incorrectas.";
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../Auth/login.php");
        exit();
    }

    public function actualizarPerfil($post) {
        $pass = !empty($post['password']) ? $post['password'] : null;
        $res = $this->model->updateProfile($_SESSION['persona_id'], $_SESSION['user_id'], $post['nombres'], $post['paterno'], $post['materno'], $pass);
        if ($res) {
            $_SESSION['nombre_completo'] = $post['nombres'] . ' ' . $post['paterno'];
            header("Location: perfil.php?msg=ok");
            exit();
        } else {
            header("Location: perfil.php?msg=error");
            exit();
        }
    }
}
?>