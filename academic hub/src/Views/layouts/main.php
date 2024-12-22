<!DOCTYPE html>
<html lang="es">
    <!-- este archivo es src/Views/layouts/main.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Softhub') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <div class="nav-content">
                    <h1>Softhub</h1>
                    <ul class="nav-menu">
                        <li><a href="<?= BASE_URL ?>/" class="nav-link">Inicio</a></li>
                        <li><a href="<?= BASE_URL ?>/login" class="nav-link">Iniciar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-2">
        <?= $content ?>
    </main>

    <footer class="mt-2">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Softhub. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>