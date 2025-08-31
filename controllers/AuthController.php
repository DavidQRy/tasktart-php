<?php
require_once __DIR__.'/../models/User.php';

class AuthController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    // Registro de usuario
    public function register(array $data): bool {
        if (empty($data['nombre']) || empty($data['correo']) || empty($data['password'])) {
            return false; // Datos incompletos
        }

        return $this->user->registrarUsuario(
            $data['nombre'],
            $data['correo'],
            $data['password']
        );
    }

    // Login de usuario (ahora guarda roles en session)
    public function login(array $data): ?array {
        if (empty($data['correo']) || empty($data['password'])) {
            return null;
        }

        $user = $this->user->loginUsuario($data['correo'], $data['password']);

        if ($user) {
            // Guardamos la sesión (sin datos sensibles)
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $_SESSION['usuario'] = $user;
            return $user;
        }

        return null;
    }

    // Cerrar sesión
    public function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_destroy();
    }
}
?>
