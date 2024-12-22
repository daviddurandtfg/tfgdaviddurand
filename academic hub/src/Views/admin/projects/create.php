<?php
use Softhub\Core\Session;
?>
<!-- src/Views/admin/projects/create.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Crear Proyecto</h1>
        <a href="<?= BASE_URL ?>/admin/projects" class="btn btn-secondary">
            Volver al listado
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/projects/create" method="POST" class="mt-4">
        <?php require '_form.php'; ?>

        <div class="action-section mt-4">
            <button type="submit" class="btn btn-primary">Crear Proyecto</button>
        </div>
    </form>
</div>