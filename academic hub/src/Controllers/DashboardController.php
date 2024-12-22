<?php
namespace Softhub\Controllers;
// este archivo es src/Controllers/DashboardController.php
use Softhub\Core\Controller;
use Softhub\Core\Session;

class DashboardController extends Controller {
    public function __construct() {
        parent::__construct();

        // Verificar que el usuario esté logueado
        if (!Session::get('user_id')) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function index() {
        return $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'username' => Session::get('username')
        ]);
    }
}