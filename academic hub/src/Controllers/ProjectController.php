<?php
namespace Softhub\Controllers;
// estea rchivo es src/Controllers/ProjectController.php
use Softhub\Core\Controller;
use Softhub\Core\Session;
use Softhub\Core\Database;
use Softhub\Core\View;
use Softhub\Models\ProjectModel;
use Softhub\Models\SoftwareModel;

class ProjectController extends Controller {
    protected $projectModel;
    protected $softwareModel;

    public function __construct() {
        parent::__construct();
        View::setLayout('admin');
        // Verificar si el usuario está autenticado y es admin
        if (!Session::get('user_id') || Session::get('user_role') !== 'administrador') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $this->projectModel = new ProjectModel($db);
        $this->softwareModel = new SoftwareModel($db);
    }

    public function index() {
        try {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $projects = $this->projectModel->getAllProjects($page);

            return $this->render('admin/projects/index', [
                'title' => 'Gestión de Proyectos',
                'projects' => $projects,
                'success' => Session::getFlash('success'),
                'error' => Session::getFlash('error')
            ]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            return $this->render('admin/projects/index', [
                'title' => 'Gestión de Proyectos',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $projectData = [
                    'codigo' => strtoupper(filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_STRING)),
                    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                    'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                    'software' => $_POST['software'] ?? [],
                    'usuario_id' => Session::get('user_id')
                ];

                // Verificar si el código ya existe
                if ($this->projectModel->codeExists($projectData['codigo'])) {
                    throw new \Exception('El código del proyecto ya existe');
                }

                $this->projectModel->create($projectData);
                Session::setFlash('success', 'Proyecto creado exitosamente');
                header('Location: ' . BASE_URL . '/admin/projects');
                exit;
            } catch (\Exception $e) {
                $availableSoftware = $this->projectModel->getAvailableSoftware();
                return $this->render('admin/projects/create', [
                    'title' => 'Crear Proyecto',
                    'error' => $e->getMessage(),
                    'oldInput' => $_POST,
                    'availableSoftware' => $availableSoftware
                ]);
            }
        }

        $availableSoftware = $this->projectModel->getAvailableSoftware();
        return $this->render('admin/projects/create', [
            'title' => 'Crear Proyecto',
            'availableSoftware' => $availableSoftware
        ]);
    }

    public function edit($params) {
        try {
            $id = $params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de proyecto no proporcionado');
            }

            $project = $this->projectModel->findById($id);
            if (!$project) {
                throw new \Exception('Proyecto no encontrado');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $projectData = [
                    'codigo' => strtoupper(filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_STRING)),
                    'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                    'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                    'software' => $_POST['software'] ?? [],
                    'usuario_id' => Session::get('user_id')
                ];

                // Verificar si el código ya existe (excluyendo el proyecto actual)
                if ($projectData['codigo'] !== $project['codigo'] &&
                    $this->projectModel->codeExists($projectData['codigo'], $id)) {
                    throw new \Exception('El código del proyecto ya existe');
                }

                $this->projectModel->update($id, $projectData);
                Session::setFlash('success', 'Proyecto actualizado exitosamente');
                header('Location: ' . BASE_URL . '/admin/projects');
                exit;
            }

            $availableSoftware = $this->projectModel->getAvailableSoftware($id);
            return $this->render('admin/projects/edit', [
                'title' => 'Editar Proyecto',
                'project' => $project,
                'availableSoftware' => $availableSoftware
            ]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/projects');
            exit;
        }
    }

    public function view($params) {
        try {
            $id = $params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de proyecto no proporcionado');
            }

            $project = $this->projectModel->findById($id);
            if (!$project) {
                throw new \Exception('Proyecto no encontrado');
            }

            return $this->render('admin/projects/view', [
                'title' => 'Detalles del Proyecto',
                'project' => $project
            ]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/projects');
            exit;
        }
    }

    public function toggleStatus($params) {
        try {
            $id = $params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de proyecto no proporcionado');
            }

            $project = $this->projectModel->findById($id);
            if (!$project) {
                throw new \Exception('Proyecto no encontrado');
            }

            $this->projectModel->toggleStatus($id);
            Session::setFlash('success', 'Estado del proyecto actualizado exitosamente');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/admin/projects');
        exit;
    }

    public function assignSoftware($params) {
        try {
            $projectId = $params['id'] ?? null;
            if (!$projectId) {
                throw new \Exception('ID de proyecto no proporcionado');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Método no permitido');
            }

            $softwareIds = $_POST['software'] ?? [];
            $this->projectModel->update($projectId, [
                'software' => $softwareIds,
                'usuario_id' => Session::get('user_id')
            ]);

            Session::setFlash('success', 'Software asignado exitosamente');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . "/admin/projects/edit/{$projectId}");
        exit;
    }
}