<?php
require_once __DIR__ . '/../config/conn.php';

class Tarea {
    private $conn;

    public function __construct() {
        $db = new DataBase(); // Tu clase de conexión
        $this->conn = $db->connect(); // Retorna un objeto mysqli
    }

    // Obtener todas las tareas con info de usuario y proyecto
    public function obtenerTareas() {
        $sql = "SELECT t.*, u.nombre AS asignado, p.nombre AS proyecto
                FROM tareas t
                LEFT JOIN usuarios u ON t.id_asignado = u.id_usuario
                LEFT JOIN proyectos p ON t.id_proyecto = p.id_proyecto
                ORDER BY t.fecha_creacion DESC";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

public function contarTareas($id_proyecto = null) {
    $sql = "SELECT 
                SUM(estado='Pendiente') AS Pendiente,
                SUM(estado='En progreso') AS `En progreso`,
                SUM(estado='Completada') AS Completada
            FROM tareas";
    if ($id_proyecto) {
        $sql .= " WHERE id_proyecto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_proyecto);
    } else {
        $stmt = $this->conn->prepare($sql);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ?: ['Pendiente'=>0,'En progreso'=>0,'Completada'=>0];
}

public function obtenerPorEstado($estado, $id_proyecto = null) {
    $sql = "SELECT t.*, u.nombre AS asignado 
            FROM tareas t 
            LEFT JOIN usuarios u ON t.id_asignado = u.id_usuario
            WHERE t.estado = ?";
    if ($id_proyecto) $sql .= " AND t.id_proyecto = ?";
    $stmt = $this->conn->prepare($sql);
    if ($id_proyecto) $stmt->bind_param("si", $estado, $id_proyecto);
    else $stmt->bind_param("s", $estado);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    // Crear tarea
    public function crear($titulo, $descripcion, $estado, $prioridad, $fecha_limite, $id_proyecto, $id_asignado) {
        $sql = "INSERT INTO tareas (titulo, descripcion, estado, prioridad, fecha_limite, id_proyecto, id_asignado)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        // Si viene vacío, poner NULL
        if ($id_asignado === "" || $id_asignado === null) {
            $id_asignado = null;
        }

        $stmt->bind_param(
            "ssssssi",
            $titulo,
            $descripcion,
            $estado,
            $prioridad,
            $fecha_limite,
            $id_proyecto,
            $id_asignado
        );

        return $stmt->execute();
    }


    // Actualizar tarea
    public function actualizar($id_tarea, $titulo, $descripcion, $estado, $prioridad, $fecha_limite, $id_asignado) {
        $sql = "UPDATE tareas 
                SET titulo=?, descripcion=?, estado=?, prioridad=?, fecha_limite=?, id_asignado=? 
                WHERE id_tarea=?";
        $stmt = $this->conn->prepare($sql);
    
        if ($id_asignado === "" || $id_asignado === null) {
            $id_asignado = null;
        }
    
        $stmt->bind_param(
            "ssssssi",
            $titulo,
            $descripcion,
            $estado,
            $prioridad,
            $fecha_limite,
            $id_asignado,
            $id_tarea
        );
    
        return $stmt->execute();
    }


    // Eliminar tarea
    public function eliminar($id_tarea) {
        $sql = "DELETE FROM tareas WHERE id_tarea = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_tarea);
        return $stmt->execute();
    }
}
