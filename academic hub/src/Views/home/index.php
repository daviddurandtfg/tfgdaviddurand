<?php
/**
 * Vista principal de la página de inicio
 * Ubicación: src/Views/home/index.php
 */
?>
<div class="welcome">
    <h2><?= htmlspecialchars($title ?? 'Bienvenido') ?></h2>
    <p><?= htmlspecialchars($description ?? 'Página de inicio') ?></p>
</div>