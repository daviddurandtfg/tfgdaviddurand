<?php
/**
 * Vista de login
 * Ubicación: src/Views/auth/login.php
 */
?>
<div class="login-container">
    <div class="login-box">
        <h2 class="text-center mb-2">Iniciar Sesión</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-control"
                       required 
                       autofocus
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control"
                       required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>