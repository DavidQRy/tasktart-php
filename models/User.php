<?php
require_once __DIR__.'/../config/conn.php';

class User {
    private ?mysqli $conn;

    public function __construct(){
        $database = new DataBase();
        $this->conn = $database->connect();
    }

    // Registrar Usuario
    public function registrarUsuario(string $nombre, string $correo, string $password): bool {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, correo, `contraseña`) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Error preparando registrarUsuario: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("sss", $nombre, $correo, $password_hash);

        return $stmt->execute();
    }

    // Login Usuario -> devuelve usuario + roles (sin la contraseña)
    public function loginUsuario(string $correo, string $password): ?array {
        $sql = "SELECT id_usuario, nombre, correo, `contraseña` AS password_hash, fecha_registro
                FROM usuarios
                WHERE correo = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando loginUsuario: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("s", $correo);
        if (!$stmt->execute()) {
            error_log("Error ejecutando loginUsuario: " . $stmt->error);
            return null;
        }

        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                // Construimos el usuario sin la contraseña
                $user = [
                    'id_usuario' => (int)$row['id_usuario'],
                    'nombre' => $row['nombre'],
                    'correo' => $row['correo'],
                    'fecha_registro' => $row['fecha_registro'],
                ];

                // Añadimos roles
                $roles = $this->obtenerRolesPorUsuario((int)$row['id_usuario']);
                $roleNames = array_map(function($r){ return $r['nombre_rol']; }, $roles);
                $roleIds = array_map(function($r){ return (int)$r['id_rol']; }, $roles);

                $user['roles'] = $roleNames; // ej: ['Administrador', 'Colaborador']
                $user['roles_ids'] = $roleIds; // ej: [1,3]
                $user['roles_string'] = empty($roleNames) ? '' : implode(', ', $roleNames);

                return $user;
            }
        }

        return null;
    }

    // Obtener todos los roles (para select)
    public function obtenerRoles(): array {
        $roles = [];
        $sql = "SELECT id_rol, nombre_rol FROM roles ORDER BY id_rol";
        $result = $this->conn->query($sql);
        if ($result) {
            while ($r = $result->fetch_assoc()) {
                $roles[] = $r;
            }
        }
        return $roles;
    }

    // Obtener roles asignados a un usuario (array de arrays id_rol, nombre_rol)
    public function obtenerRolesPorUsuario(int $id_usuario): array {
        $roles = [];
        $sql = "SELECT r.id_rol, r.nombre_rol
                FROM usuario_rol ur
                JOIN roles r ON ur.id_rol = r.id_rol
                WHERE ur.id_usuario = ? AND ur.activo = TRUE";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando obtenerRolesPorUsuario: " . $this->conn->error);
            return $roles;
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $roles[] = $row;
        }
        return $roles;
    }

    // Verificar si el usuario es Administrador (por nombre de rol)
    public function esAdmin(int $id_usuario): bool {
        $roles = $this->obtenerRolesPorUsuario($id_usuario);
        foreach ($roles as $r) {
            if (strcasecmp($r['nombre_rol'], 'Administrador') === 0) {
                return true;
            }
        }
        return false;
    }

    // Obtener todos los usuarios con sus roles (roles concatenados)
    public function obtenerUsuariosConRoles(): array {
        $usuarios = [];

        $sql = "SELECT u.id_usuario, u.nombre, u.correo, u.fecha_registro,
                       GROUP_CONCAT(r.nombre_rol SEPARATOR ', ') AS roles
                FROM usuarios u
                LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario AND ur.activo = TRUE
                LEFT JOIN roles r ON ur.id_rol = r.id_rol
                GROUP BY u.id_usuario, u.nombre, u.correo, u.fecha_registro
                ORDER BY u.id_usuario";

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    // Obtener usuario por id (sin roles)
    public function getUserById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT id_usuario, nombre, correo, fecha_registro FROM usuarios WHERE id_usuario = ? LIMIT 1");
        if (!$stmt) {
            error_log("Error preparando getUserById: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            return $res->fetch_assoc();
        }
        return null;
    }

    // Actualizar usuario (nombre, correo) y opcionalmente contraseña
    public function updateUsuario(int $id, string $nombre, string $correo, ?string $newPassword = null): bool {
        if (!empty($newPassword)) {
            $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre = ?, correo = ?, `contraseña` = ? WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Error preparando updateUsuario (with pass): " . $this->conn->error);
                return false;
            }
            $stmt->bind_param("sssi", $nombre, $correo, $password_hash, $id);
        } else {
            $sql = "UPDATE usuarios SET nombre = ?, correo = ? WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Error preparando updateUsuario: " . $this->conn->error);
                return false;
            }
            $stmt->bind_param("ssi", $nombre, $correo, $id);
        }

        return $stmt->execute();
    }

    // Asignar un rol único al usuario: elimina roles previos y asigna el nuevo (si $id_rol es null borra todos)
    public function setUserRole(int $id_usuario, ?int $id_rol): bool {
        // iniciamos transacción por seguridad
        $this->conn->begin_transaction();
        try {
            // Eliminar (o marcar inactivo) roles anteriores. Aquí borro.
            $del = $this->conn->prepare("DELETE FROM usuario_rol WHERE id_usuario = ?");
            if (!$del) throw new Exception("Error preparando delete usuario_rol: " . $this->conn->error);
            $del->bind_param("i", $id_usuario);
            $del->execute();

            if ($id_rol !== null && $id_rol > 0) {
                $ins = $this->conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol, activo) VALUES (?, ?, TRUE)");
                if (!$ins) throw new Exception("Error preparando insert usuario_rol: " . $this->conn->error);
                $ins->bind_param("ii", $id_usuario, $id_rol);
                $ins->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error en setUserRole: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerRolUsuario(int $id_usuario): ?string {
        $sql = "SELECT r.nombre_rol 
                FROM usuario_rol ur 
                INNER JOIN roles r ON ur.id_rol = r.id_rol
                WHERE ur.id_usuario = ? AND ur.activo = 1
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando obtenerRolUsuario: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $row = $res->fetch_assoc()) {
            return $row['nombre_rol'];
        }

        return null;
    }

    public function obtenerPlanActivo() {
        $sql = "SELECT p.nombre 
                FROM pagos pg
                INNER JOIN planes p ON pg.id_plan = p.id_plan
                WHERE pg.estado = 'Completado'
                ORDER BY pg.fecha_pago DESC
                LIMIT 1";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch_assoc()['nombre'] ?? 'Gratis';
    }

    public function obtenerUsuariosPorProyecto($id_proyecto) {
    $sql = "SELECT u.id_usuario, u.nombre, up.rol_en_proyecto 
            FROM usuario_proyecto up
            INNER JOIN usuarios u ON u.id_usuario = up.id_usuario
            WHERE up.id_proyecto = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $id_proyecto);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function obtenerRolUsuarioProyecto($id_usuario, $id_proyecto) {
    $sql = "SELECT rol_en_proyecto FROM usuario_proyecto 
            WHERE id_usuario = ? AND id_proyecto = ? LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $id_usuario, $id_proyecto);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['rol_en_proyecto'] : 'Invitado';
}

public function obtenerPlanUsuario($id_usuario) {
    $sql = "SELECT p.nombre AS plan_nombre, p.limite_proyectos 
            FROM pagos pa
            INNER JOIN planes p ON pa.id_plan = p.id_plan
            WHERE pa.id_usuario = ? 
            ORDER BY pa.fecha_pago DESC LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ?? ['plan_nombre' => 'Gratis', 'limite_proyectos' => 1];
}


}
?>
