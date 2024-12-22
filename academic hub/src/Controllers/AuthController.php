<?php
namespace Softhub\Controllers;
// este archivo es src/Controllers/AuthController.php
use Softhub\Core\Controller;
use Softhub\Core\CSRF;
use Softhub\Core\Session;
use Softhub\Core\Database;
use Softhub\Models\UserModel;

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        Session::start();

        // Obtener la conexión de la base de datos
        $db = Database::getInstance()->getConnection();

        // Inicializar el modelo de usuarios con la conexión
        $this->userModel = new UserModel($db);
    }

    public function login() {
        // Redirigir si ya está autenticado
        if (Session::get('user_id')) {
            $redirect = Session::get('user_role') === 'administrador' ? '/admin/dashboard' : '/dashboard';
            header('Location: ' . BASE_URL . $redirect);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }

        // GET request - mostrar formulario
        $token = CSRF::generateToken();
        return $this->render('auth/login', [
            'csrf_token' => $token,
            'title' => 'Iniciar Sesión',
            'error' => Session::getFlash('error')
        ]);
    }

    private function handleLogin() {
        try {
            // Validar CSRF token
            if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
                throw new \Exception('Token de seguridad inválido');
            }

            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                throw new \Exception('Por favor complete todos los campos');
            }

            $user = $this->userModel->validateCredentials($username, $password);

            if (!$user) {
                // Registrar intento fallido
                $this->userModel->logAuthAction(null, 'fallido', $username);
                throw new \Exception('Credenciales inválidas');
            }

            if (!$user['activo']) {
                throw new \Exception('Esta cuenta está desactivada');
            }

            // Iniciar sesión
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('username', $user['nombre_usuario']);
            Session::set('user_role', $user['rol']);

            // Actualizar último login
            $this->userModel->updateLastLogin($user['id']);

            // Registrar el login exitoso
            $this->userModel->logAuthAction($user['id'], 'login');

            // Redirigir según rol
            $redirect = $user['rol'] === 'administrador' ? '/admin/dashboard' : '/dashboard';
            header('Location: ' . BASE_URL . $redirect);
            exit;

        } catch (\Exception $e) {
            // Generar nuevo token para el formulario
            $token = CSRF::generateToken();
            return $this->render('auth/login', [
                'error' => $e->getMessage(),
                'csrf_token' => $token,
                'title' => 'Iniciar Sesión'
            ]);
        }
    }

    public function logout() {
        if (Session::get('user_id')) {
            // Registrar el logout
            $this->userModel->logAuthAction(Session::get('user_id'), 'logout');
        }

        Session::destroy();
        Session::setFlash('success', 'Sesión cerrada exitosamente');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function forbidden() {
        http_response_code(403);
        return $this->render('auth/forbidden', [
            'title' => 'Acceso Denegado'
        ]);
    }
}