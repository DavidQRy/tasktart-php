<?php
require_once __DIR__ . '/../config/conn.php';

class Proyecto {
    private mysqli $conn;

    public function __construct() {
        $database = new DataBase();
        $this->conn = $database->connect(); // Retorna conexión mysqli
    }

    // 🔹 Proyectos creados por el usuario
    public function obtenerProyectosCreados(int $id_usuario): array {
        $proyectos = [];
        $sql = "SELECT * FROM proyectos WHERE id_creador = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $proyectos[] = $row;
            }
            $stmt->close();
        }
        return $proyectos;
    }

    // 🔹 Proyectos donde el usuario participa
    public function obtenerProyectosUnidos(int $id_usuario): array {
        $proyectos = [];
        $sql = "SELECT p.*, up.rol_en_proyecto 
                FROM usuario_proyecto up
                INNER JOIN proyectos p ON p.id_proyecto = up.id_proyecto
                WHERE up.id_usuario = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $proyectos[] = $row;
            }
            $stmt->close();
        }
        return $proyectos;
    }

    // 🔹 Proyectos disponibles para unirse
    public function obtenerProyectosDisponibles(int $id_usuario): array {
        $proyectos = [];
        $sql = "SELECT p.* 
                FROM proyectos p
                WHERE p.id_proyecto NOT IN (
                    SELECT id_proyecto FROM usuario_proyecto WHERE id_usuario = ?
                )";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $proyectos[] = $row;
            }
            $stmt->close();
        }
        return $proyectos;
    }

    // 🔹 Unirse a un proyecto
    public function unirseAProyecto(int $id_usuario, int $id_proyecto): bool {
        $sql = "INSERT INTO usuario_proyecto (id_usuario, id_proyecto) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $id_usuario, $id_proyecto);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }

        error_log("Error en unirseAProyecto: " . $this->conn->error);
        return false;
    }

    public function crearProyecto(int $id_creador, string $nombre, string $descripcion, ?string $fecha_inicio, ?string $fecha_fin): bool {
    $sql = "INSERT INTO proyectos (id_creador, nombre, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("issss", $id_creador, $nombre, $descripcion, $fecha_inicio, $fecha_fin);
        return $stmt->execute();
    }
    return false;
}

}
?>
