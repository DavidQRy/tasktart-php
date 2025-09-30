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
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->modelo->crear(
                $_POST['titulo'],
                $_POST['descripcion'],
                $_POST['estado'],
                $_POST['prioridad'],
                $_POST['fecha_limite'],
                $_POST['id_proyecto'],
                $_POST['id_asignado']
            );
            header("Location: tareas.php");
        }
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
