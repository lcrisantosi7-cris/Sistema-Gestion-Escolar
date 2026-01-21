<?php
require_once '../../Models/Administracion/Personal.php';
require_once '../../Models/Administracion/Rol.php';

class PersonalController {
    
    private $modPersonal;
    private $modRol;

    public function __construct() {
        $this->modPersonal = new Personal();
        $this->modRol = new Rol();
    }

    public function index() {
        $busqueda = $_POST['busqueda'] ?? '';
        $personal = $this->modPersonal->listar($busqueda);
        return ['personal' => $personal, 'busqueda' => $busqueda];
    }

    // Datos para el formulario (Roles y datos si es editar)
    public function form($idPersonal = null) {
        $roles = $this->modRol->listarTodos();
        $datos = null;
        if ($idPersonal) {
            $datos = $this->modPersonal->obtenerPorId($idPersonal);
        }
        return ['roles' => $roles, 'datos' => $datos];
    }

    public function guardar($post) {
        $idPersonal = $post['idPersonal'] ?? null;
        $res = false;

        if ($idPersonal) {
            $res = $this->modPersonal->actualizar($post);
        } else {
            $res = $this->modPersonal->registrar($post);
        }

        if ($res === true) {
            header("Location: index.php?msg=guardado");
        } else {
            // Manejo básico de error
            echo "<script>alert('$res'); window.history.back();</script>";
        }
    }

    public function eliminar($id) {
        $this->modPersonal->eliminar($id);
        header("Location: index.php?msg=eliminado");
    }
}
?>