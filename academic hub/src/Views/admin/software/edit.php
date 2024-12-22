<?php
use Softhub\Core\Session;
?>
<!-- src/Views/admin/software/edit.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Editar Software</h1>
        <a href="<?= BASE_URL ?>/admin/software" class="btn btn-secondary">
            Volver al listado
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/software/edit/<?= $software['id'] ?>" method="POST" class="mt-2">
        <?php require '_form.php'; ?>

        <div class="action-section mt-2">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>