<?php
if (!isset($software)) {
    $software = [];
}
?>
<!-- src/Views/admin/software/_form.php -->
<div class="form-group mb-4">
    <label for="nombre" class="block mb-2">Nombre del Software</label>
    <input type="text"
           name="nombre"
           id="nombre"
           class="form-control w-full p-2 border rounded"
           value="<?= htmlspecialchars($software['nombre'] ?? '') ?>"
           required>
</div>

<div class="form-group mb-4">
    <label for="descripcion" class="block mb-2">Descripción</label>
    <textarea name="descripcion"
              id="descripcion"
              class="form-control w-full p-2 border rounded"
              rows="4"><?= htmlspecialchars($software['descripcion'] ?? '') ?></textarea>
</div>

<div class="form-group mb-4">
    <label for="categoria" class="block mb-2">Categoría</label>
    <select name="categoria" 
            id="categoria" 
            class="form-control w-full p-2 border rounded" 
            required>
        <option value="">Seleccionar categoría</option>
        <option value="desarrollo" <?= (($software['categoria'] ?? '') === 'desarrollo') ? 'selected' : '' ?>>Desarrollo</option>
        <option value="ofimatica" <?= (($software['categoria'] ?? '') === 'ofimatica') ? 'selected' : '' ?>>Ofimática</option>
        <option value="diseño" <?= (($software['categoria'] ?? '') === 'diseño') ? 'selected' : '' ?>>Diseño</option>
        <option value="utilidades" <?= (($software['categoria'] ?? '') === 'utilidades') ? 'selected' : '' ?>>Utilidades</option>
        <option value="seguridad" <?= (($software['categoria'] ?? '') === 'seguridad') ? 'selected' : '' ?>>Seguridad</option>
    </select>
</div>

<div class="form-group mb-4">
    <label for="version" class="block mb-2">Versión</label>
    <input type="text"
           name="version"
           id="version"
           class="form-control w-full p-2 border rounded"
           value="<?= htmlspecialchars($software['version'] ?? '') ?>"
           required>
</div>