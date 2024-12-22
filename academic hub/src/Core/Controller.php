<?php
namespace Softhub\Core;
//este archivo es src/Core/Controller.php
abstract class Controller {
    public function __construct() {
    }

    protected function render($view, $data = []) {
        error_log("=== RENDER EN CONTROLLER ===", 3, SRC_PATH . "/../logs/app.log");
        error_log("Vista: " . $view, 3, SRC_PATH . "/../logs/app.log");
        error_log("Layout actual: " . View::getLayout(), 3, SRC_PATH . "/../logs/app.log");
        return View::render($view, $data);
    }
}