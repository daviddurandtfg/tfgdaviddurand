<?php
namespace Softhub\Controllers;
// este archivo es src/Controllers/SoftwareController.php
use Softhub\Core\Controller;
use Softhub\Core\Session;
use Softhub\Core\Database;
use Softhub\Core\View;
use Softhub\Models\SoftwareModel;

class SoftwareController extends Controller {
    protected $softwareModel;
    protected $db;

    public function __construct() {
        parent::__construct();
        View::setLayout('admin');

        // Verificar si el usuario está autenticado y es admin
        if (!Session::get('user_id') || Session::get('user_role') !== 'administrador') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->db = Database::getInstance()->getConnection();
        $this->softwareModel = new SoftwareModel($this->db);
    }

    public function index() {
        try {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $software = $this->softwareModel->getAllSoftware($page);

            return $this->render('admin/software/index', [
                'title' => 'Gestión de Software',
                'software' => $software,
                'success' => Session::getFlash('success'),
                'error' => Session::getFlash('error')
            ]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            return $this->render('admin/software/index', [
                'title' => 'Gestión de Software',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $softwareData = [
                    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                    'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                    'categoria' => filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING),
                    'version' => filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING)
                ];

                $this->softwareModel->createSoftware($softwareData);
                Session::setFlash('success', 'Software creado exitosamente');
                header('Location: ' . BASE_URL . '/admin/software');
                exit;
            } catch (\Exception $e) {
                return $this->render('admin/software/create', [
                    'title' => 'Crear Software',
                    'error' => $e->getMessage(),
                    'oldInput' => $_POST
                ]);
            }
        }

        return $this->render('admin/software/create', [
            'title' => 'Crear Software'
        ]);
    }

    public function edit($params) {
        try {
            $id = $params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de software no proporcionado');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $softwareData = [
                    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                    'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                    'categoria' => filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING),
                    'version' => filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING)
                ];

                $this->softwareModel->updateSoftware($id, $softwareData);
                Session::setFlash('success', 'Software actualizado exitosamente');
                header('Location: ' . BASE_URL . '/admin/software');
                exit;
            }

            $software = $this->softwareModel->findById($id);
            if (!$software) {
                throw new \Exception('Software no encontrado');
            }

            return $this->render('admin/software/edit', [
                'title' => 'Editar Software',
                'software' => $software
            ]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/software');
            exit;
        }
    }

    public function delete($params) {
        try {
            $id = $params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de software no proporcionado');
            }

            $this->softwareModel->deleteSoftware($id);
            Session::setFlash('success', 'Software eliminado exitosamente');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/admin/software');
        exit;
    }

    public function toggleStatus($params) {
        try {
            $id = $params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de software no proporcionado');
            }

            $software = $this->softwareModel->findById($id);
            if (!$software) {
                throw new \Exception('Software no encontrado');
            }

            $this->softwareModel->toggleStatus($id);
            Session::setFlash('success', 'Estado del software actualizado exitosamente.');

        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/admin/software');
        exit;
    }
}