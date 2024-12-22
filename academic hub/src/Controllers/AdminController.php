<?php
namespace Softhub\Controllers;
// este archivo es src/Controllers/AdminController.php
use Softhub\Core\Controller;
use Softhub\Core\Database;
use Softhub\Core\Session;
use Softhub\Core\View;
use Softhub\Models\UserModel;

class AdminController extends Controller {
    protected $userModel;

    public function __construct() {
        parent::__construct();
        View::setLayout('admin');

        if (!Session::get('user_id') || Session::get('user_role') !== 'administrador') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $this->userModel = new UserModel($db);
    }

    public function index() {
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }

    public function dashboard() {
        try {
            $stats = [
                'total_users' => $this->userModel->countUsers(),
                'active_users' => $this->userModel->countUsers(true),
                'admin_users' => $this->userModel->countUsersByRole('administrador'),
                'recent_logins' => $this->userModel->getRecentLogins(5)
            ];

            error_log("Stats recuperadas: " . print_r($stats, true));

            return $this->render('admin/dashboard', [
                'title' => 'Dashboard Administrativo',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            error_log("Error en dashboard: " . $e->getMessage());
            Session::setFlash('error', 'Error al cargar las estadísticas');
            return $this->render('admin/dashboard', [
                'title' => 'Dashboard Administrativo',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function users() {
        try {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $users = $this->userModel->findAll($page);

            error_log("Usuarios recuperados: " . print_r($users, true));

            return $this->render('admin/users/index', [
                'title' => 'Gestión de Usuarios',
                'users' => $users['users'],
                'totalPages' => $users['pages'],
                'currentPage' => $page,
                'success' => Session::getFlash('success'),
                'error' => Session::getFlash('error')
            ]);
        } catch (\Exception $e) {
            error_log("Error en users: " . $e->getMessage());
            Session::setFlash('error', $e->getMessage());
            return $this->render('admin/users/index', [
                'title' => 'Gestión de Usuarios',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userData = [
                    'nombre_usuario' => filter_input(INPUT_POST, 'nombre_usuario', FILTER_SANITIZE_STRING),
                    'contraseña' => password_hash($_POST['contraseña'] ?? '', PASSWORD_DEFAULT),
                    'rol' => filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_STRING),
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];

                $this->userModel->create($userData);
                Session::setFlash('success', 'Usuario creado exitosamente');
                header('Location: ' . BASE_URL . '/admin/users');
                exit;
            } catch (\Exception $e) {
                return $this->render('admin/users/create', [
                    'title' => 'Crear Usuario',
                    'error' => $e->getMessage(),
                    'oldInput' => $_POST
                ]);
            }
        }

        return $this->render('admin/users/create', [
            'title' => 'Crear Usuario'
        ]);
    }

    public function editUser($params = []) {
          error_log("Parámetros recibidos en editUser: " . print_r($params, true));
        try {
            $id = isset($params['id']) ? $params['id'] : null;
            if (!$id) {
                throw new \Exception('ID de usuario no proporcionado');
                error_log("ID extraído: " . ($id ?? 'null'));
            }

            $user = $this->userModel->findById($id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userData = [
                    'nombre_usuario' => filter_input(INPUT_POST, 'nombre_usuario', FILTER_SANITIZE_STRING),
                    'rol' => filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_STRING),
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];

                if (!empty($_POST['contraseña'])) {
                    $userData['contraseña'] = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
                }

                $this->userModel->update($id, $userData);
                Session::setFlash('success', 'Usuario actualizado exitosamente');
                header('Location: ' . BASE_URL . '/admin/users');
                exit;
            }

            return $this->render('admin/users/edit', [
                'title' => 'Editar Usuario',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }
    }

    public function deleteUser($params = []) {
        try {
            $id = isset($params['id']) ? $params['id'] : null;
            if (!$id) {
                throw new \Exception('ID de usuario no proporcionado');
            }

            if ($id == Session::get('user_id')) {
                throw new \Exception('No puedes eliminar tu propia cuenta');
            }

            $this->userModel->delete($id);
            Session::setFlash('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function toggleUserStatus($params = []) {
        try {
            $id = isset($params['id']) ? $params['id'] : null;
            if (!$id) {
                throw new \Exception('ID de usuario no proporcionado');
            }

            $user = $this->userModel->findById($id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }

            $newStatus = !$user['activo'];
            $this->userModel->update($id, ['activo' => $newStatus]);
            Session::setFlash('success', 'Estado del usuario actualizado exitosamente');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }
}