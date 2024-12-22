<?php use Softhub\Core\View; ?>
<!-- este archivo es src/Views/admin/users/index.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Gestión de Usuarios</h1>
        <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">
            Crear Usuario
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Login</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($user['rol']) ?></td>
                    <td>
                        <span class="<?= $user['activo'] ? 'text-success' : 'text-danger' ?>">
                            <?= $user['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <?= $user['ultimo_login'] ? htmlspecialchars($user['ultimo_login']) : 'Nunca' ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= BASE_URL ?>/admin/users/edit/<?= $user['id'] ?>" 
                               class="btn btn-secondary">Editar</a>
                            
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            <a href="<?= BASE_URL ?>/admin/users/toggle/<?= $user['id'] ?>" 
                               class="btn <?= $user['activo'] ? 'btn-danger' : 'btn-primary' ?>"
                               onclick="return confirm('¿Estás seguro?')">
                                <?= $user['activo'] ? 'Desactivar' : 'Activar' ?>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/users/delete/<?= $user['id'] ?>" 
                               class="btn btn-danger"
                               onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                Eliminar
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>