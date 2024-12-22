<?php use Softhub\Core\View; ?>
<!-- este archivo es src/Views/admin/users/create.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Crear Usuario</h1>
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">
            Volver al listado
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/users/create" method="POST">
        <?php require '_form.php'; ?>

        <div class="action-section">
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </div>
    </form>
</div>