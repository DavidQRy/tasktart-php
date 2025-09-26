<?php
session_start();
require_once __DIR__ . "/config/conn.php";

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['usuario'];

// Conectar DB
$database = new DataBase();
$conn = $database->connect();

// Consultar historial de pagos del usuario logueado
// Consultar pagos con información del plan
$sql = "SELECT p.id_pago, pl.nombre AS plan, pl.precio, p.fecha_pago, p.estado, p.referencia
        FROM pagos p
        INNER JOIN planes pl ON p.id_plan = pl.id_plan
        WHERE p.id_usuario = ? 
        ORDER BY p.fecha_pago DESC";
$id_usuario = $user['id_usuario'];
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
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
</head>
<body class="bg-light">
    
    <!-- Header -->
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
                        <i class="fas fa-credit-card me-2"></i>Mis Pagos
                    </h2>
                    <a href="planes.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Comprar Plan
                    </a>
                </div>
                
                <!-- Tarjeta de Resumen (solo si hay pagos) -->
                <?php if ($result->num_rows > 0): 
                    // Calcular estadísticas simples
                    $total_pagos = $result->num_rows;
                    $result->data_seek(0); // Resetear el puntero para poder iterar de nuevo
                    $total_gastado = 0;
                    $pagos_completados = 0;
                    
                    while ($row = $result->fetch_assoc()) {
                        $total_gastado += $row['precio'];
                        if ($row['estado'] == 'Completado') {
                            $pagos_completados++;
                        }
                    }
                    $result->data_seek(0); // Resetear de nuevo para la tabla
                ?>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card payment-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Total Pagos</h5>
                                        <h3 class="mb-0"><?php echo $total_pagos; ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-receipt fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card payment-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Completados</h5>
                                        <h3 class="mb-0"><?php echo $pagos_completados; ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card payment-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Total Gastado</h5>
                                        <h3 class="mb-0">$<?php echo number_format($total_gastado, 2); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Tabla de Pagos -->
                <div class="card payment-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Historial de Pagos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Plan</th>
                                            <th scope="col">Precio</th>
                                            <th scope="col">Fecha de Pago</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Referencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <th scope="row"><?php echo $row['id_pago']; ?></th>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-cube me-2 text-primary"></i>
                                                        <?php echo htmlspecialchars($row['plan']); ?>
                                                    </div>
                                                </td>
                                                <td><strong>$<?php echo number_format($row['precio'], 2); ?></strong></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['fecha_pago'])); ?></td>
                                                <td>
                                                    <?php if ($row['estado'] == 'Completado'): ?>
                                                        <span class="badge status-badge bg-success">
                                                            <i class="fas fa-check me-1"></i> Completado
                                                        </span>
                                                    <?php elseif ($row['estado'] == 'Pendiente'): ?>
                                                        <span class="badge status-badge bg-warning text-dark">
                                                            <i class="fas fa-clock me-1"></i> Pendiente
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge status-badge bg-danger">
                                                            <i class="fas fa-times me-1"></i> Fallido
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($row['referencia']); ?></code>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-credit-card"></i>
                                <h4>No hay pagos registrados</h4>
                                <p class="text-muted">Aún no has realizado ningún pago en TaskTart.</p>
                                <a href="planes.php" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-1"></i> Ver Planes Disponibles
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <?php if ($result->num_rows > 0): ?>
                <div class="mt-4">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2 fa-lg"></i>
                        <div>
                            <strong>Información importante:</strong> Si tienes algún problema con un pago, por favor contacta a nuestro soporte técnico.
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>