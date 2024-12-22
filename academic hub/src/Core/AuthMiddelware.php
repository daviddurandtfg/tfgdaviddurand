<?php

namespace Softhub\Core;
// <!-- este archivo es src/Core/AuthMiddleware.php -->
class AuthMiddleware {
    public static function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireAdmin() {
        self::requireAuth();
        if ($_SESSION['role'] !== 'administrador') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}
