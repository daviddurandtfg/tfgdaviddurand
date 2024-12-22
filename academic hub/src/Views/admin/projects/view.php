<?php
use Softhub\Core\Session;
?>
<!-- src/Views/admin/projects/view.php -->
<div class="card">
    <div class="mb-2 flex justify-between items-center">
        <h1 class="card-title">Detalles del Proyecto</h1>
        <div>
            <a href="<?= BASE_URL ?>/admin/projects/edit/<?= $project['id'] ?>" class="btn btn-secondary">
                Editar
            </a>
            <a href="<?= BASE_URL ?>/admin/projects" class="btn btn-secondary">
                Volver al listado
            </a>
        </div>
    </div>

    <div class="project-details mt-4">
        <div class="grid grid-2 gap-4">
            <div class="info-group">
                <label class="font-bold">Código:</label>
                <p><?= htmlspecialchars($project['codigo']) ?></p>
            </div>

            <div class="info-group">
                <label class="font-bold">Estado:</label>
                <p>
                    <span class="status-badge <?= $project['activo'] ? 'status-active' : 'status-inactive' ?>">
                        <?= $project['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </p>
            </div>

            <div class="info-group col-span-2">
                <label class="font-bold">Nombre:</label>
                <p><?= htmlspecialchars($project['nombre']) ?></p>
            </div>

            <div class="info-group col-span-2">
                <label class="font-bold">Descripción:</label>
                <p><?= nl2br(htmlspecialchars($project['descripcion'])) ?></p>
            </div>
        </div>

        <div class="software-section mt-4">
            <h2 class="text-xl font-bold mb-2">Software Asignado</h2>

            <?php if (!empty($project['software'])): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Versión</th>
                                <th>Asignado por</th>
                                <th>Fecha de Asignación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($project['software'] as $software): ?>
                                <tr>
                                    <td><?= htmlspecialchars($software['nombre']) ?></td>
                                    <td><?= htmlspecialchars($software['version']) ?></td>
                                    <td><?= htmlspecialchars($software['asignado_por']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($software['asignado_en']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No hay software asignado a este proyecto.</p>
            <?php endif; ?>
        </div>
    </div>
</div>