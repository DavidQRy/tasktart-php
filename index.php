<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./visual/css/index.css">
    <title>TaskTart - Gestor de Tareas</title>

</head>
<body>
    <!-- Header -->
    <header class="header">
        <h1>TaskTart</h1>
        <nav class="navBar">
            <a href="#home">Inicio</a>
            <a href="#features">Servicios</a>
            <a href="#about">Nosotros</a>
            <a href="#contact">Contacto</a>
            <a href="login.php" style="background-color: white; color:black;">Iniciar sesión</a>
            <a href="register.php">Registrarse</a>
        </nav>
    </header>

    <!-- Dark Mode Toggle -->
    <button class="dark-toggle" onclick="toggleDarkMode()" title="Cambiar tema">
        🌙
    </button>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <!-- Floating Elements -->
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        
        <div class="hero-content">
            <h2>Organiza tu vida, simplifica tu día.</h2>
            <p>Tasktart es tu asistente digital para gestionar tareas y finanzas en un solo lugar.</p>
            <div class="cta-buttons">
                <a href="#contact" class="btn-primary">Comienza Gratis</a>
                <a href="#features" class="btn-secondary">Conocer Más</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">¿Por Qué Elegirnos?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔔</div>
                    <h3>Nunca más olvides nada</h3>
                    <p>Crea recordatorios personalizados para tus actividades diarias.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Control total de tus finanzas</h3>
                    <p>Registra ingresos, gastos y ahorros de manera sencilla.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Accede desde cualquier dispositivo </h3>
                    <p>Disponible como app y página web para que siempre tengas tus planes a la mano.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Organización sin esfuerzo</h3>
                    <p>Una herramienta pensada para mantenerte enfocado y productivo.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Seguridad Total</h3>
                    <p>Implementamos los más altos estándares de seguridad para proteger tu información y la de tus usuarios.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Soporte 24/7</h3>
                    <p>Nuestro equipo está disponible en todo momento para brindarte el apoyo que necesitas, cuando lo necesitas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="features" id="about">
        <div class="container">
            <h2 class="section-title">¿Qué es TaskTart?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Con TaskTart Podras:</h3>
                    <p> 
                         ✅ Mantener tu vida en orden: Crea y organiza tareas con recordatorios inteligentes para que nunca olvides nada.
                                    💸 Controlar tus finanzas sin estrés: Registra gastos, planea ahorros y sigue el progreso de tus metas.
                         📱 Gestionar desde cualquier lugar: Una interfaz ágil y minimalista que funciona perfecto en tu celular o computador.
                </p>
                </div>
                

            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="hero" id="contact">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        
        <div class="hero-content">
            <h2>¿Listo para Comenzar?</h2>
            <p>Contáctanos hoy mismo y descubre cómo podemos transformar tu visión en una realidad digital exitosa. Nuestro equipo está esperando tu proyecto.</p>
            <div class="cta-buttons">
                <a href="mailto:info@innovatech.com" class="btn-primary">Enviar Email</a>
                <a href="tel:+1234567890" class="btn-secondary">Llamar Ahora</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: rgba(0,0,0,0.8); padding: 40px 20px; text-align: center; color: var(--color-blanco);">
        <div class="container">
            <p>&copy; 2024 InnovaTech. Todos los derechos reservados.</p>
            <p style="margin-top: 10px;">Transformando el futuro digital, un proyecto a la vez.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Dark mode toggle function
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const darkToggle = document.querySelector('.dark-toggle');
            
            if (document.body.classList.contains('dark-mode')) {
                darkToggle.textContent = '☀️';
            } else {
                darkToggle.textContent = '🌙';
            }
        }

        // Check for saved dark mode preference on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize dark mode toggle
            const darkToggle = document.querySelector('.dark-toggle');
            darkToggle.textContent = '🌙';
            
            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Observe feature cards for animation
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.feature-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'linear-gradient(135deg, rgba(232, 133, 79, 0.95), rgba(214, 116, 56, 0.95))';
            } else {
                header.style.background = 'linear-gradient(135deg, #e8854f, #d67438)';
            }
        });

        // Parallax effect for floating shapes
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const shapes = document.querySelectorAll('.floating-shape');
            
            shapes.forEach((shape, index) => {
                const speed = 0.5 + (index * 0.2);
                shape.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Add hover effects for buttons
        document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.animation = 'none';
            });
        });
    </script>
</body>
</html>