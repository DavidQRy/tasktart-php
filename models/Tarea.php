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

    // Obtener tareas por estado con nombre del asignado y proyecto
    public function obtenerPorEstado($estado) {
        $sql = "SELECT t.*, u.nombre AS asignado, p.nombre AS proyecto
                FROM tareas t
                LEFT JOIN usuarios u ON t.id_asignado = u.id_usuario
                LEFT JOIN proyectos p ON t.id_proyecto = p.id_proyecto
                WHERE t.estado = ?
                ORDER BY t.fecha_limite ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $estado);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // Contadores
    public function contarTareas() {
        $sql = "SELECT estado, COUNT(*) as total FROM tareas GROUP BY estado";
        $result = $this->conn->query($sql);

        $conteo = ["Pendiente" => 0, "En progreso" => 0, "Completada" => 0];
        while ($row = $result->fetch_assoc()) {
            $conteo[$row['estado']] = $row['total'];
        }
        return $conteo;
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
