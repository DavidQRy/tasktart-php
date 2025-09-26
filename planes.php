<?php
session_start();
require_once __DIR__ . "/models/Transaccion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$transaccion = new Transaccion();
$planes = $transaccion->obtenerPlanes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="visual/css/planes.css">
    <title>Planes Disponibles - TaskTart</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Header -->
    <header class="header text-white shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <h1 class="h3 mb-0">TaskTart</h1>
                <nav class=" navBar d-flex align-items-center">
                    <a href="dashboard.php" class="text-white text-decoration-none me-3">Dashboard</a>
                    <a href="#" class="text-white text-decoration-none me-3">Tareas</a>
                    <a href="#" class="text-white text-decoration-none me-3">Perfil</a>
                    <a href="mis_pagos.php" class="text-white text-decoration-none me-3">Mis Pagos</a>
                    <a href="logout.php" class="btn btn-sm me-3">Cerrar sesión</a>
                    <button class="btn btn-outline-light btn-sm" onclick="document.body.classList.toggle('dark-mode')">
                        <i class="fas fa-moon"></i> / <i class="fas fa-sun"></i>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-5 p-5 g-3">
        <div class="row">
            <div class="col-12 bg-white mb-2 rounded-3">
                <h2 class="section-title">Elige tu Plan</h2>
                <p class="section-subtitle">Selecciona el plan que mejor se adapte a tus necesidades</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <?php foreach($planes as $plan): 
                // Determinar clase CSS según el nombre del plan
                $planClass = '';
                if (strpos($plan['nombre'], 'Gratis') !== false) {
                    $planClass = 'plan-free';
                } elseif (strpos($plan['nombre'], 'Starter') !== false) {
                    $planClass = 'plan-starter';
                } elseif (strpos($plan['nombre'], 'Pro') !== false && strpos($plan['nombre'], 'Anual') === false) {
                    $planClass = 'plan-pro';
                } elseif (strpos($plan['nombre'], 'Business') !== false) {
                    $planClass = 'plan-business';
                } elseif (strpos($plan['nombre'], 'Anual') !== false) {
                    $planClass = 'plan-anual';
                }
                
                // Determinar si es recomendado
                $isRecommended = (strpos($plan['nombre'], 'Pro') !== false && strpos($plan['nombre'], 'Anual') === false);
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card plan-card h-100 p-1 <?php echo $planClass; ?>">
                    <?php if($isRecommended): ?>
                    <div class="recommended-badge my-3">Recomendado</div>
                    <?php endif; ?>
                    
                    <div class="plan-header rounded">
                        <h3 class="h4"><?php echo $plan['nombre']; ?></h3>
                        <div class="plan-price">
                            $<?php echo $plan['precio']; ?>
                        </div>
                        <div class="plan-period">
                            <?php echo $plan['duracion_dias']; ?> días
                        </div>
                    </div>
                    
                    <div class="plan-features">
                        <p class="text-muted"><?php echo $plan['descripcion']; ?></p>
                        <ul>
                            <?php
                            // Extraer características de la descripción
                            $features = explode('.', $plan['descripcion']);
                            foreach($features as $feature):
                                $feature = trim($feature);
                                if(!empty($feature) && $feature != ''):

                            ?>
                            <li><i class="fas fa-check"></i> <?php echo $feature; ?>.</li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    
                    <div class="plan-action">
                        <form method="POST" action="procesar_pago.php">
                            <input type="hidden" name="id_plan" value="<?php echo $plan['id_plan']; ?>">
                            <button type="submit" class="btn btn-primary w-100">
                                <?php echo $plan['precio'] == 0 ? 'Seleccionar Gratis' : 'Comprar Ahora'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Información adicional -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Preguntas Frecuentes</h5>
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        ¿Puedo cambiar de plan en cualquier momento?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Sí, puedes cambiar de plan en cualquier momento. Al cambiar a un plan superior, se aplicará un prorrateo del costo. Al cambiar a un plan inferior, el cambio se aplicará al final de tu ciclo de facturación actual.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        ¿Ofrecen descuentos para equipos?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Sí, ofrecemos descuentos especiales para equipos de más de 10 usuarios. Contáctanos para obtener más información sobre nuestros precios empresariales.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        ¿Qué métodos de pago aceptan?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Aceptamos tarjetas de crédito (Visa, MasterCard, American Express), PayPal y transferencias bancarias para planes anuales.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>TaskTart</h5>
                    <p>La mejor herramienta para gestionar tus tareas y proyectos.</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Inicio</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Características</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Precios</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> soporte@tasktart.com</li>
                        <li><i class="fas fa-phone me-2"></i> +1 (555) 123-4567</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2023 TaskTart. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>