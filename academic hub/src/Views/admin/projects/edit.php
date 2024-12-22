<?php
use Softhub\Core\Session;
?>
<!-- src/Views/admin/projects/edit.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Editar Proyecto</h1>
        <div>
            <a href="<?= BASE_URL ?>/admin/projects/view/<?= $project['id'] ?>" class="btn btn-secondary">
                Ver Detalles
            </a>
            <a href="<?= BASE_URL ?>/admin/projects" class="btn btn-secondary">
                Volver al listado
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/projects/edit/<?= $project['id'] ?>" method="POST" class="mt-4">
        <?php require '_form.php'; ?>

        <div class="action-section mt-4">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>