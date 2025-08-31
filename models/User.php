<?php 
require_once __DIR__.'/../config/conn.php';

class User {
    private ?mysqli $conn;
    
    public function __construct(){
        $database = new DataBase;
        $this->conn = $database->connect();
    }

    // ✅ Registrar Usuario
    public function registrarUsuario(string $nombre, string $correo, string $password): bool {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Error preparando la consulta: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("sss", $nombre, $correo, $password_hash);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            return false;
        }
    }

    // ✅ Login Usuario
    public function loginUsuario(string $correo, string $password): ?array {
        $sql = "SELECT id_usuario, nombre, correo, contraseña, fecha_registro
                FROM usuarios
                WHERE correo = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Error preparando la consulta: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("s", $correo);

        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            return null;
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['contraseña'])) {
                return $user; // Se devuelve el usuario logueado
            }
        }

        return null;
    }
    // Verificar si el usuario es Administrador
    public function esAdmin(int $id_usuario): bool {
        $sql = "SELECT r.nombre_rol 
                FROM usuario_rol ur
                JOIN roles r ON ur.id_rol = r.id_rol
                WHERE ur.id_usuario = ? AND ur.activo = TRUE";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($row['nombre_rol'] === 'Administrador') {
                return true;
            }
        }
        return false;
    }

    // Obtener todos los usuarios
    public function obtenerUsuarios(): array {
        $usuarios = [];
        $sql = "SELECT id_usuario, nombre, correo, fecha_registro FROM usuarios";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }
    // Obtener todos los usuarios con su(s) rol(es)
    public function obtenerUsuariosConRoles(): array {
        $usuarios = [];

        $sql = "SELECT u.id_usuario, u.nombre, u.correo, u.fecha_registro, 
                       GROUP_CONCAT(r.nombre_rol SEPARATOR ', ') AS roles
                FROM usuarios u
                LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario
                LEFT JOIN roles r ON ur.id_rol = r.id_rol
                GROUP BY u.id_usuario, u.nombre, u.correo, u.fecha_registro";

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateUser($id, $nombre, $email, $id_rol) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, id_rol = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssii", $nombre, $email, $id_rol, $id);
        return $stmt->execute();
    }


}
?>
