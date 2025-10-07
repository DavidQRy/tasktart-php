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
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $prioridad = $_POST['prioridad'];
    $fecha_limite = $_POST['fecha_limite'];
    $id_asignado = $_POST['id_asignado'] ?: null;
    $id_proyecto = $_POST['id_proyecto'] ?? null;

    // ✅ Orden correcto de los parámetros
    $this->modelo->crear($titulo, $descripcion, $estado, $prioridad, $fecha_limite, $id_proyecto, $id_asignado);
    header("Location: tareas.php?proyecto=$id_proyecto");
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
