<?php
use Softhub\Core\Session;
?>
<!-- src/Views/admin/projects/index.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Gestión de Proyectos</h1>
        <a href="<?= BASE_URL ?>/admin/projects/create" class="btn btn-primary">
            Crear Proyecto
        </a>
    </div>

    <?php if (Session::getFlash('success')): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars(Session::getFlash('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($projects['projects'])): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Software Asignado</th>
                        <th>Estado</th>
                        <th>Última Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects['projects'] as $project): ?>
                        <tr>
                            <td><?= htmlspecialchars($project['codigo']) ?></td>
                            <td><?= htmlspecialchars($project['nombre']) ?></td>
                            <td class="text-center"><?= $project['total_software'] ?></td>
                            <td>
                                <span class="status-badge <?= $project['activo'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $project['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($project['actualizado_en']))) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>/admin/projects/view/<?= $project['id'] ?>"
                                       class="btn btn-secondary btn-sm">Ver</a>
                                    <a href="<?= BASE_URL ?>/admin/projects/edit/<?= $project['id'] ?>"
                                       class="btn btn-secondary btn-sm">Editar</a>
                                    <a href="<?= BASE_URL ?>/admin/projects/toggle/<?= $project['id'] ?>"
                                       class="btn <?= $project['activo'] ? 'btn-warning' : 'btn-success' ?> btn-sm"
                                       onclick="return confirm('¿Estás seguro?')">
                                        <?= $project['activo'] ? 'Desactivar' : 'Activar' ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($projects['pages'] > 1): ?>
            <div class="pagination mt-4">
                <!-- TODO: Implementar paginación -->
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center mt-4">
            <p class="text-muted">No hay proyectos registrados</p>
        </div>
    <?php endif; ?>
</div>