<?php
if (!isset($project)) {
    $project = [];
}
?>
<!-- src/Views/admin/projects/_form.php -->
<div class="form-group">
    <label for="codigo">Código del Proyecto</label>
    <input type="text"
           name="codigo"
           id="codigo"
           class="form-control"
           value="<?= htmlspecialchars($project['codigo'] ?? '') ?>"
           required
           pattern="[A-Z0-9]{3,10}"
           title="3-10 caracteres alfanuméricos en mayúsculas"
           <?= isset($project['id']) ? 'readonly' : '' ?>>
    <small class="text-muted">Código único de 3-10 caracteres (mayúsculas y números)</small>
</div>

<div class="form-group">
    <label for="nombre">Nombre del Proyecto</label>
    <input type="text"
           name="nombre"
           id="nombre"
           class="form-control"
           value="<?= htmlspecialchars($project['nombre'] ?? '') ?>"
           required>
</div>

<div class="form-group">
    <label for="descripcion">Descripción</label>
    <textarea name="descripcion"
              id="descripcion"
              class="form-control"
              rows="4"><?= htmlspecialchars($project['descripcion'] ?? '') ?></textarea>
</div>

<div class="form-group">
    <label>Software Asignado</label>
    <div class="grid grid-2 gap-4">
        <?php foreach ($availableSoftware as $software): ?>
            <div class="checkbox-container">
                <input type="checkbox"
                       name="software[]"
                       value="<?= $software['id'] ?>"
                       id="software_<?= $software['id'] ?>"
                       <?= isset($software['asignado']) && $software['asignado'] ? 'checked' : '' ?>>
                <label class="checkbox-label" for="software_<?= $software['id'] ?>">
                    <?= htmlspecialchars($software['nombre']) ?>
                    <small class="text-muted">(<?= htmlspecialchars($software['version']) ?>)</small>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
</div>