<?php
require_once __DIR__ . '/../models/Tarea.php';

class TareaController {
    public $modelo;

    public function __construct() {
        $this->modelo = new Tarea();
    }

    public function index() {
        $conteo = $this->modelo->contarTareas();
        $pendientes = $this->modelo->obtenerPorEstado("Pendiente");
        $progreso = $this->modelo->obtenerPorEstado("En progreso");
        $completadas = $this->modelo->obtenerPorEstado("Completada");

        include __DIR__ . '/../views/tareas/dashboard.php';
    }

public function store() {
    require_once __DIR__ . '/../models/User.php';
    $usuarioModel = new User();

    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $estado = $_POST['estado'] ?? 'Pendiente';
    $prioridad = $_POST['prioridad'] ?? 'Media';
    $fecha_limite = $_POST['fecha_limite'] ?? null;
    $id_asignado = $_POST['id_asignado'] ?: null;
    $id_proyecto = $_POST['id_proyecto'] ?: null;
    $id_creador = $_SESSION['usuario']['id_usuario'];

    if (empty($id_proyecto)) {
        echo "<script>alert('Selecciona un proyecto antes de crear una tarea.'); window.history.back();</script>";
        exit;
    }

    // 🔹 Validar plan del usuario
    $plan = $usuarioModel->obtenerPlanUsuario($id_creador);
    $limiteTareas = $plan['limite_tareas'] ?? 0; // 0 = ilimitadas
    $tareasActuales = $this->modelo->contarTareasPorUsuario($id_creador);

    if ($limiteTareas != 0 && $tareasActuales >= $limiteTareas) {
        echo "<script>alert('Has alcanzado el límite de tareas de tu plan: {$plan['plan_nombre']}'); window.location.href='tareas.php';</script>";
        exit;
    }

    // 🔹 Crear tarea
    $this->modelo->crear($titulo, $descripcion, $estado, $prioridad, $fecha_limite, $id_proyecto, $id_asignado, $id_creador);

    header("Location: tareas.php?msg=Tarea creada con éxito");
    exit;
}




    public function update($id_tarea) {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->modelo->actualizar(
                $id_tarea,
                $_POST['titulo'],
                $_POST['descripcion'],
                $_POST['estado'],
                $_POST['prioridad'],
                $_POST['fecha_limite'],
                $_POST['id_asignado']
            );
            header("Location: tareas.php");
        }
    }

    public function delete($id_tarea) {
        $this->modelo->eliminar($id_tarea);
        header("Location: tareas.php");
    }
}
