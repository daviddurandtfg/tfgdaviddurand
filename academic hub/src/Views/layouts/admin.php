<?php use Softhub\Core\View; ?>
<!-- este archivo es src/Views/layouts/admin.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Panel de Administración' ?> - Softhub</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="container">
            <div class="nav-content">
                <div class="nav-brand">
                    <h1>Softhub Admin</h1>
                </div>

                <ul class="nav-menu">
                    <li><a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link">Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/users" class="nav-link">Usuarios</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/software" class="nav-link">Software</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/logs" class="nav-link">Logs</a></li>
                </ul>

                <div class="user-nav">
                    <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-danger">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mt-2">
        <?= $content ?>
    </main>

    <footer class="footer mt-2">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Softhub. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>