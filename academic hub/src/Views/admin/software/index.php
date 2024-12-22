<?php
use Softhub\Core\Session;
?>
<!-- src/Views/admin/software/index.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Gestión de Software</h1>
        <a href="<?= BASE_URL ?>/admin/software/create" class="btn btn-primary">
            Agregar Software
        </a>
    </div>

    <?php if (Session::getFlash('success')): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars(Session::getFlash('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (Session::getFlash('error')): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars(Session::getFlash('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($software['software'])): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Versión</th>
                        <th>Estado</th>
                        <th>Última Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($software['software'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nombre']) ?></td>
                            <td><?= htmlspecialchars($item['categoria']) ?></td>
                            <td><?= htmlspecialchars($item['version']) ?></td>
                            <td>
                                <span class="status-badge <?= $item['activo'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $item['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($item['actualizado_en']))) ?></td>
                            <td class="action-buttons">
                                <a href="<?= BASE_URL ?>/admin/software/edit/<?= $item['id'] ?>"
                                   class="btn btn-secondary btn-sm">Editar</a>
                                <a href="<?= BASE_URL ?>/admin/software/toggle/<?= $item['id'] ?>"
                                   class="btn <?= $item['activo'] ? 'btn-danger' : 'btn-primary' ?> btn-sm"
                                   onclick="return confirm('¿Estás seguro de <?= $item['activo'] ? 'desactivar' : 'activar' ?> este software? <?= $item['activo'] ? '\n\nNota: Esto desvinculará el software de todos los proyectos asociados.' : '' ?>')">
                                    <?= $item['activo'] ? 'Desactivar' : 'Activar' ?>
                                </a>
                                <a href="<?= BASE_URL ?>/admin/software/delete/<?= $item['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Estás seguro de eliminar este software? Esta acción desvinculara el software de todos los proyectos asociados y no se puede deshacer.')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($software['pages'] > 1): ?>
            <div class="pagination mt-4">
                <!-- TODO: Implementar paginación -->
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center mt-4">
            <p class="text-muted">No hay software registrado</p>
        </div>
    <?php endif; ?>
</div>