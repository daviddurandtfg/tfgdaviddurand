<?php if (!isset($user)) $user = []; ?>

<div class="form-group">
    <label for="nombre_usuario">Nombre de Usuario</label>
    <input type="text"
           name="nombre_usuario"
           id="nombre_usuario"
           class="form-control"
           value="<?= htmlspecialchars($user['nombre_usuario'] ?? '') ?>"
           required>
</div>

<div class="form-group">
    <label for="contraseña">Contraseña</label>
    <input type="password"
           name="contraseña"
           id="contraseña"
           class="form-control"
           <?= isset($user['id']) ? '' : 'required' ?>>
    <?php if (isset($user['id'])): ?>
        <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
    <?php endif; ?>
</div>

<div class="form-group">
    <label for="rol">Rol</label>
    <select name="rol" 
            id="rol" 
            class="form-control" 
            required>
        <option value="usuario" <?= (($user['rol'] ?? '') === 'usuario') ? 'selected' : '' ?>>
            Usuario
        </option>
        <option value="administrador" <?= (($user['rol'] ?? '') === 'administrador') ? 'selected' : '' ?>>
            Administrador
        </option>
    </select>
</div>

<div class="form-group">
    <label class="checkbox-container">
        <input type="checkbox" 
               name="activo" 
               <?= (!isset($user['id']) || ($user['activo'] ?? false)) ? 'checked' : '' ?>>
        <span class="checkbox-label">Usuario Activo</span>
    </label>
</div>