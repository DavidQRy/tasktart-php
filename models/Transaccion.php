<?php
require_once __DIR__ . '/../config/conn.php';

class Transaccion {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->connect();
    }

    public function obtenerPlanes() {
        $result = $this->conn->query("SELECT * FROM planes");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function registrarPago($id_usuario, $id_plan, $referencia) {
        $stmt = $this->conn->prepare(
            "INSERT INTO pagos (id_usuario, id_plan, referencia, estado) VALUES (?, ?, ?, 'Pendiente')"
        );
        $stmt->bind_param("iis", $id_usuario, $id_plan, $referencia);
        return $stmt->execute();
    }

    public function completarPago($id_pago) {
        $stmt = $this->conn->prepare("UPDATE pagos SET estado = 'Completado' WHERE id_pago = ?");
        $stmt->bind_param("i", $id_pago);
        return $stmt->execute();
    }

    public function obtenerPagosPorUsuario($id_usuario) {
        $stmt = $this->conn->prepare(
            "SELECT p.id_pago, pl.nombre AS plan, pl.precio, p.fecha_pago, p.estado
             FROM pagos p
             JOIN planes pl ON p.id_plan = pl.id_plan
             WHERE p.id_usuario = ?"
        );
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
