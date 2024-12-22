<?php
namespace Softhub\Core;
//este archivo es src/Core/Session.php
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar cookies seguras de sesión
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');
            session_start();
        }
    }

    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key) {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy(): void {
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Nuevos métodos para mensajes flash
    public static function setFlash(string $key, $message): void {
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash(string $key) {
        if (isset($_SESSION['_flash'][$key])) {
            $message = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        return null;
    }

    public static function hasFlash(string $key): bool {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function clearFlash(): void {
        $_SESSION['_flash'] = [];
    }
}