<?php use Softhub\Core\Session; ?>
<!-- este archivo es src/Views/admin/dashboard.php -->
<div class="card">
    <h1 class="card-title">Dashboard Administrativo</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (Session::getFlash('success')): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars(Session::getFlash('success')) ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stats-card">
            <h3 class="card-title">Usuarios Totales</h3>
            <p class="stats-number"><?= $stats['total_users'] ?? 0 ?></p>
        </div>

        <div class="stats-card">
            <h3 class="card-title">Usuarios Activos</h3>
            <p class="stats-number"><?= $stats['active_users'] ?? 0 ?></p>
        </div>

        <div class="stats-card">
            <h3 class="card-title">Administradores</h3>
            <p class="stats-number"><?= $stats['admin_users'] ?? 0 ?></p>
        </div>
    </div>

    <div class="card mt-2">
        <h2 class="card-title">Logins Recientes</h2>
        <?php if (!empty($stats['recent_logins'])): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_logins'] as $login): ?>
                            <tr>
                                <td><?= htmlspecialchars($login['nombre_usuario']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($login['timestamp']))) ?></td>
                                <td><?= htmlspecialchars($login['ip_address']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No hay registros de login recientes</p>
        <?php endif; ?>
    </div>

    <div class="action-section mt-2">
        <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">Crear Nuevo Usuario</a>
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-secondary">Gestionar Usuarios</a>
    </div>
</div>