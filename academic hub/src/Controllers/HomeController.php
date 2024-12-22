<?php
namespace Softhub\Controllers;
// este archivo es src/controllers/HomeController.php
use Softhub\Core\Controller;

class HomeController extends Controller {
    public function index() {
    var_dump([
        'BASE_URL' => BASE_URL,
        'Document Root' => $_SERVER['DOCUMENT_ROOT'],
        'Script Filename' => $_SERVER['SCRIPT_FILENAME'],
        'CSS Path' => $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/css/styles.css'
    ]);

    return $this->render('home/index', [
        'title' => 'Bienvenido a Softhub',
        'description' => 'Sistema de gestión de software corporativo'
    ]);
}
}