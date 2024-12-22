<?php
namespace Softhub\Core;

//este archivo es src/Core/Auth.php
use Softhub\Models\UserModel;

class Auth {
    private $userModel;
    private const MAX_ATTEMPTS = 5;
    private const BLOCK_DURATION = 900; // 15 minutos en segundos

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function authenticate(string $username, string $password): bool {
        $user = $this->userModel->findByUsername($username);

        if (!$user || !$user['activo']) {
            $this->handleFailedAttempt($username);
            return false;
        }

        if (!password_verify($password, $user['contraseña'])) {
            $this->handleFailedAttempt($username);
            return false;
        }

        // Autenticación exitosa
        $this->clearLoginAttempts($username);
        $this->initializeSession($user);

        return true;
    }

    private function handleFailedAttempt(string $username): void {
        $ip = $_SERVER['REMOTE_ADDR'];

        // Registrar intento fallido
        $this->logAuthEvent($username, 'fallido');

        // Incrementar contador de intentos
        $attempts = $this->userModel->incrementLoginAttempts($username, $ip);

        // Bloquear si excede intentos máximos
        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->blockUser($username, $ip);
            $this->logAuthEvent($username, 'bloqueado');
        }
    }

    public function isUserBlocked(string $username, string $ip): bool {
        $blockInfo = $this->userModel->getLoginAttempts($username, $ip);

        if (!$blockInfo) {
            return false;
        }

        // Verificar si existe bloqueo activo
        if ($blockInfo['bloqueado_hasta'] &&
            new \DateTime($blockInfo['bloqueado_hasta']) > new \DateTime()) {
            return true;
        }

        // Si el bloqueo expiró, limpiar intentos
        $this->clearLoginAttempts($username, $ip);
        return false;
    }

    private function blockUser(string $username, string $ip): void {
        $blockUntil = new \DateTime();
        $blockUntil->modify('+' . self::BLOCK_DURATION . ' seconds');

        $this->userModel->blockUser($username, $ip, $blockUntil);
    }

    private function clearLoginAttempts(string $username, string $ip = null): void {
        $this->userModel->clearLoginAttempts($username, $ip);
    }

    private function initializeSession(array $user): void {
        // Regenerar ID de sesión por seguridad
        Session::regenerate();

        // Almacenar datos básicos en sesión
        Session::set('user_id', $user['id']);
        Session::set('username', $user['nombre_usuario']);
        Session::set('user_role', $user['rol']);
        Session::set('last_activity', time());
    }

    public function isAuthenticated(): bool {
        if (!Session::get('user_id')) {
            return false;
        }

        // Verificar tiempo de inactividad (30 minutos)
        $lastActivity = Session::get('last_activity');
        if (time() - $lastActivity > 1800) {
            Session::destroy();
            return false;
        }

        // Actualizar tiempo de última actividad
        Session::set('last_activity', time());
        return true;
    }

    public function logAuthEvent(string $username, string $action): void {
        $userId = Session::get('user_id');
        $data = [
            'usuario_id' => $userId,
            'nombre_usuario' => $username,
            'accion' => $action,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        $this->userModel->logAuthEvent($data);
    }
}