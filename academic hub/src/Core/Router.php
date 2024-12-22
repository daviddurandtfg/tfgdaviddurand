<?php
namespace Softhub\Core;
// este archivo es src/Core/Router.php
class Router {
    private $routes = [];
    private $params = [];
    private $namespace = 'Softhub\\Controllers\\';

    public function add($route, $params = []) {
        // Manejar la ruta raíz especialmente
        if ($route === '') {
            $route = '/^$/';
        } else {
            // Convertir la ruta en una expresión regular
            $route = preg_replace('/\//', '\\/', $route);
            $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
            $route = '/^' . $route . '$/i';
        }
        $this->routes[$route] = $params;
    }

    public function match($url) {
        error_log("MATCH - URL recibida: " . $url);
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                error_log("MATCH - Coincidencia encontrada en ruta: " . $route);
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                error_log("MATCH - Parámetros: " . print_r($this->params, true));
                return true;
            }
        }
        return false;
    }

    public function getParams() {
        return $this->params;
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function dispatch($url) {
        if ($this->match($url)) {
            $controller = $this->params['controller'] ?? 'Home';
            $controller = $this->namespace . $controller . 'Controller';

            if (class_exists($controller)) {
                $controller_object = new $controller();
                $action = $this->params['action'] ?? 'index';
                $action = $this->convertToCamelCase($action);

                // Verificar si la ruta requiere autenticación
                if ($this->requiresAuth($controller, $action) && !$this->isAuthenticated()) {
                    header('Location: ' . BASE_URL . '/login');
                    exit;
                }

                if (method_exists($controller_object, $action)) {
                    echo $controller_object->$action($this->params);
                    return true;
                } else {
                    throw new \Exception("Método $action no encontrado en $controller");
                }
            } else {
                throw new \Exception("Controlador $controller no encontrado");
            }
        }
        throw new \Exception("Ruta no encontrada: '$url'");
    }

    protected function convertToCamelCase($string) {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    protected function requiresAuth($controller, $action) {
        // Lista de rutas públicas (no requieren autenticación)
        $publicRoutes = [
            'Softhub\\Controllers\\AuthController' => ['login'],
            'Softhub\\Controllers\\HomeController' => ['index']
        ];

        if (isset($publicRoutes[$controller])) {
            return !in_array($action, $publicRoutes[$controller]);
        }

        // Por defecto, todas las demás rutas requieren autenticación
        return true;
    }

    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}