<?php
session_start();
require_once __DIR__ . "/config/conn.php";

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];
$id_usuario = $user['id_usuario'];

// Conectar DB
$database = new DataBase();
$conn = $database->connect();

// Obtener rol del usuario
$sqlRol = "SELECT r.nombre_rol 
           FROM usuario_rol ur 
           INNER JOIN roles r ON ur.id_rol = r.id_rol
           WHERE ur.id_usuario = ? AND ur.activo = 1
           LIMIT 1";
$stmtRol = $conn->prepare($sqlRol);
$stmtRol->bind_param("i", $id_usuario);
$stmtRol->execute();
$resultRol = $stmtRol->get_result();
$rolUsuario = $resultRol->fetch_assoc()['nombre_rol'] ?? null;

// Roles permitidos
$rolesPermitidos = ['Administrador', 'Project Manager'];
if (!in_array($rolUsuario, $rolesPermitidos) && $rolUsuario !== 'Colaborador') {
    echo "<script>alert('No tienes permisos para acceder a esta sección');window.location='dashboard.php';</script>";
    exit;
}

// Confirmar pago (solo admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pago']) && $rolUsuario === 'Administrador') {
    $id_pago = intval($_POST['id_pago']);
    $sqlUpdate = "UPDATE pagos SET estado='Completado' WHERE id_pago=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("i", $id_pago);
    if ($stmtUpdate->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Pago confirmado',
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location='mis_pagos.php');
        </script>";
    }
}

// Consultar historial de pagos
if ($rolUsuario === 'Administrador') {
    $sql = "SELECT p.id_pago, pl.nombre AS plan, pl.precio, p.fecha_pago, p.estado, p.referencia, u.nombre 
            FROM pagos p
            INNER JOIN planes pl ON p.id_plan = pl.id_plan
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha_pago DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT p.id_pago, pl.nombre AS plan, pl.precio, p.fecha_pago, p.estado, p.referencia 
            FROM pagos p
            INNER JOIN planes pl ON p.id_plan = pl.id_plan
            WHERE p.id_usuario = ?
            ORDER BY p.fecha_pago DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pagos - TaskTart</title>
    <link rel="stylesheet" href="visual/css/planes.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    <style>
        /* Estilos adicionales para mejorar la apariencia */
        .payment-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .payment-card:hover {
            transform: translateY(-2px);
        }
        
        .status-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #6c757d;
        }
    </style>
    </style>
</head>
<body class="bg-light">
    
<!-- Header -->
    <header class="header text-white shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <h1 class="h3 mb-0">TaskTart</h1>
                <nav class=" navBar d-flex align-items-center">
                    <a href="dashboard.php" class="text-white text-decoration-none me-3">Dashboard</a>
                    <a href="#" class="text-white text-decoration-none me-3">Tareas</a>
                    <a href="#" class="text-white text-decoration-none me-3">Perfil</a>
                    <a href="planes.php" class="text-white text-decoration-none me-3">Planes</a>
                    <a href="logout.php" class="btn btn-sm me-3">Cerrar sesión</a>
                    <button class="btn btn-outline-light btn-sm" onclick="document.body.classList.toggle('dark-mode')">
                        <i class="fas fa-moon"></i> / <i class="fas fa-sun"></i>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container py-5">
        <div class="row bg-white p-3 rounded">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="page-title">
                        <i class="fas fa-credit-card me-2"></i>Historial de Pagos
                    </h2>
                    <a href="planes.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Comprar Plan
                    </a>
                </div>
                
                <?php if ($result->num_rows > 0): ?>
                    <div class="card payment-card mb-4">
                        <div class="card-body row">
                            <?php
                            $total_pagos = $result->num_rows;
                            $result->data_seek(0);
                            $total_gastado = 0;
                            $pagos_completados = 0;
                            while ($rowStats = $result->fetch_assoc()) {
                                $total_gastado += $rowStats['precio'];
                                if ($rowStats['estado'] == 'Completado') $pagos_completados++;
                            }
                            $result->data_seek(0);
                            ?>
                            <div class="col-md-4">
                                <div class="card payment-card bg-primary text-white mb-3">
                                    <div class="card-body d-flex justify-content-between">
                                        <div>
                                            <h5>Total Pagos</h5>
                                            <h3><?php echo $total_pagos; ?></h3>
                                        </div>
                                        <i class="fas fa-receipt fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card payment-card bg-success text-white mb-3">
                                    <div class="card-body d-flex justify-content-between">
                                        <div>
                                            <h5>Completados</h5>
                                            <h3><?php echo $pagos_completados; ?></h3>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card payment-card bg-info text-white mb-3">
                                    <div class="card-body d-flex justify-content-between">
                                        <div>
                                            <h5>Total Gastado</h5>
                                            <h3>$<?php echo number_format($total_gastado, 2); ?></h3>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Plan</th>
                                    <th>Precio</th>
                                    <th>Fecha de Pago</th>
                                    <th>Estado</th>
                                    <th>Referencia</th>
                                    <?php if ($rolUsuario === 'Administrador'): ?>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id_pago']; ?></td>
                                        <td><?php echo htmlspecialchars($row['plan']); ?></td>
                                        <td><strong>$<?php echo number_format($row['precio'], 2); ?></strong></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_pago'])); ?></td>
                                        <td>
                                            <?php if ($row['estado'] == 'Completado'): ?>
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Completado</span>
                                            <?php elseif ($row['estado'] == 'Pendiente'): ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pendiente</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Fallido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($row['referencia']); ?></code></td>
                                        
                                        <?php if ($rolUsuario === 'Administrador'): ?>
                                            <td><?php echo htmlspecialchars($row['nombre'] ?? ''); ?></td>
                                            <td>
                                                <?php if ($row['estado'] == 'Pendiente'): ?>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id_pago" value="<?php echo $row['id_pago']; ?>">
                                                        <button type="submit" name="confirmar_pago" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Confirmar
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-credit-card"></i>
                        <h4>No hay pagos registrados</h4>
                        <p class="text-muted">Aún no se han realizado pagos en TaskTart.</p>
                        <a href="planes.php" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i> Ver Planes Disponibles
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
