<?php
namespace Softhub\Core;
//este archivo es CSRF.php
class CSRF {
    public static function generateToken(): string {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    public static function validateToken(?string $token): bool {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        $valid = hash_equals($_SESSION['csrf_token'], $token);

        // Limpiar el token después de la validación
        unset($_SESSION['csrf_token']);

        return $valid;
    }
}