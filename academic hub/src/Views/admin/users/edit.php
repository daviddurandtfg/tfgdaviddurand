<?php
use Softhub\Core\View;
?>

<!-- src/Views/admin/users/edit.php -->
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Editar Usuario</h1>
            <a href="<?= BASE_URL ?>/admin/users"
               class="text-blue-500 hover:text-blue-700">
                Volver al listado
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form action="<?= BASE_URL ?>/admin/users/edit/<?= $user['id'] ?>" method="POST">
                <?php require '_form.php'; ?>

                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>