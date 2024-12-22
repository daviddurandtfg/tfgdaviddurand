<?php
session_start();
// este archivo es public/index.php
// Cargar configuración primero
require_once dirname(__DIR__) . '/config/config.php';
// Cargar autoloader
require_once dirname(__DIR__) . '/src/Core/Autoloader.php';
// Inicializar autoloader y clases core
use Softhub\Core\Autoloader;
use Softhub\Core\Router;
use Softhub\Core\Session;
// Registrar autoloader
Autoloader::register();
// Inicializar router
$router = new Router();

// Definir rutas básicas
$router->add('', ['controller' => 'Home', 'action' => 'index']); // Ruta raíz
$router->add('login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);
$router->add('dashboard', ['controller' => 'Dashboard', 'action' => 'index']);

// Rutas de administración
$router->add('admin/dashboard', ['controller' => 'Admin', 'action' => 'dashboard']);
$router->add('admin/users', ['controller' => 'Admin', 'action' => 'users']);
$router->add('admin/logs', ['controller' => 'Admin', 'action' => 'systemLogs']);
$router->add('admin/users/create', ['controller' => 'Admin', 'action' => 'createUser']);
$router->add('admin/users/edit/{id}', ['controller' => 'Admin', 'action' => 'editUser']);
$router->add('admin/users/delete/{id}', ['controller' => 'Admin', 'action' => 'deleteUser']);
$router->add('admin/users/toggle/{id}', ['controller' => 'Admin', 'action' => 'toggleUserStatus']);

// Rutas para gestión de software
$router->add('admin/software', ['controller' => 'Software', 'action' => 'index']);
$router->add('admin/software/create', ['controller' => 'Software', 'action' => 'create']);
$router->add('admin/software/edit/{id}', ['controller' => 'Software', 'action' => 'edit']);
$router->add('admin/software/delete/{id}', ['controller' => 'Software', 'action' => 'delete']);
$router->add('admin/software/toggle/{id}', ['controller' => 'Software', 'action' => 'toggleStatus']);
$router->add('admin/software/checkDependencies/{id}', ['controller' => 'Software', 'action' => 'checkDependencies']);

// Rutas para gestión de proyectos
$router->add('admin/projects', ['controller' => 'Project', 'action' => 'index']);
$router->add('admin/projects/create', ['controller' => 'Project', 'action' => 'create']);
$router->add('admin/projects/edit/{id}', ['controller' => 'Project', 'action' => 'edit']);
$router->add('admin/projects/view/{id}', ['controller' => 'Project', 'action' => 'view']);
$router->add('admin/projects/toggle/{id}', ['controller' => 'Project', 'action' => 'toggleStatus']);

// Procesar la URL
$url = $_SERVER['REQUEST_URI'];

// Debug logs
error_log("=== PROCESANDO REQUEST ===");
error_log("REQUEST_URI original: " . $_SERVER['REQUEST_URI']);
error_log("BASE_URL value: " . BASE_URL);
error_log("URL antes de procesar: " . $url);

// Remover BASE_URL si está presente
if (!empty(BASE_URL) && strpos($url, BASE_URL) === 0) {
    $url = substr($url, strlen(BASE_URL));
}

// Limpiar la URL
$url = trim($url, '/');
error_log("URL procesada final: " . $url);
error_log("Params del router antes de match: " . print_r($router->getParams(), true));

try {
    if (!$router->match($url)) {
        error_log("No se encontró coincidencia para la URL: " . $url);
        throw new \Exception('No route matched.', 404);
    }

    // Obtener el controlador y la acción
    $params = $router->getParams();
    error_log("Params después de match: " . print_r($params, true));

    $controller = $params['controller'];
    $action = $params['action'];

    // Construir el nombre completo del controlador
    $controllerClass = "Softhub\\Controllers\\{$controller}Controller";

    if (!class_exists($controllerClass)) {
        throw new \Exception("Controller $controller not found");
    }

    $controllerInstance = new $controllerClass();

    if (!method_exists($controllerInstance, $action)) {
        throw new \Exception("Action $action not found in controller $controller");
    }

    // Ejecutar la acción
    echo $controllerInstance->$action($params);

} catch (\Exception $e) {
    http_response_code($e->getCode() === 404 ? 404 : 500);
    if (DEV_MODE) {
        echo "<h1>Error: " . htmlspecialchars($e->getMessage()) . "</h1>";
        echo "<pre>";
        echo htmlspecialchars($e->getTraceAsString());
        echo "</pre>";
    } else {
        // En producción mostrar un mensaje genérico
        echo "<h1>Error</h1>";
        echo "<p>Lo sentimos, ha ocurrido un error.</p>";
    }
    // Registrar el error
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
}