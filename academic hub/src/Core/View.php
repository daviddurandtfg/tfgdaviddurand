<?php
namespace Softhub\Core;
//este archivo es src/Core/View.php
class View {
    private static $layout = 'main';

    public static function render($view, $data = []) {
        try {
            error_log("=== INICIO RENDER ===", 3, SRC_PATH . "/../logs/app.log");
            error_log("Vista a renderizar: " . $view, 3, SRC_PATH . "/../logs/app.log");
            error_log("Layout en uso: " . self::$layout, 3, SRC_PATH . "/../logs/app.log");
            error_log("Datos pasados: " . print_r($data, true), 3, SRC_PATH . "/../logs/app.log");

            $viewFile = SRC_PATH . "/Views/{$view}.php";
            error_log("Archivo de vista: " . $viewFile, 3, SRC_PATH . "/../logs/app.log");

            if (!file_exists($viewFile)) {
                error_log("ERROR: Vista no encontrada: " . $viewFile, 3, SRC_PATH . "/../logs/app.log");
                throw new \Exception("Vista no encontrada: {$view}");
            }

            error_log("Extrayendo datos y cargando vista", 3, SRC_PATH . "/../logs/app.log");
            extract($data);
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            error_log("Vista cargada exitosamente", 3, SRC_PATH . "/../logs/app.log");

            $layoutFile = SRC_PATH . "/Views/layouts/" . self::$layout . ".php";
            error_log("Intentando cargar layout: " . $layoutFile, 3, SRC_PATH . "/../logs/app.log");

            if (!file_exists($layoutFile)) {
                error_log("ERROR: Layout no encontrado: " . $layoutFile, 3, SRC_PATH . "/../logs/app.log");
                throw new \Exception("Layout no encontrado: " . self::$layout);
            }

            error_log("Layout existe, procediendo a incluir", 3, SRC_PATH . "/../logs/app.log");
            ob_start();
            include $layoutFile;
            $result = ob_get_clean();
            error_log("=== FIN RENDER ===", 3, SRC_PATH . "/../logs/app.log");
            return $result;

        } catch (\Exception $e) {
            error_log("ERROR en render: " . $e->getMessage(), 3, SRC_PATH . "/../logs/app.log");
            throw $e;
        }
    }

    public static function partial($partial, $data = []) {
        try {
            error_log("=== INICIO PARTIAL ===", 3, SRC_PATH . "/../logs/app.log");
            error_log("Cargando partial: " . $partial, 3, SRC_PATH . "/../logs/app.log");

            $partialFile = SRC_PATH . "/Views/partials/{$partial}.php";
            error_log("Archivo partial: " . $partialFile, 3, SRC_PATH . "/../logs/app.log");

            if (!file_exists($partialFile)) {
                error_log("ERROR: Partial no encontrado: " . $partialFile, 3, SRC_PATH . "/../logs/app.log");
                throw new \Exception("Partial no encontrado: {$partial}");
            }

            error_log("Extrayendo datos y cargando partial", 3, SRC_PATH . "/../logs/app.log");
            extract($data);
            include $partialFile;
            error_log("=== FIN PARTIAL ===", 3, SRC_PATH . "/../logs/app.log");

        } catch (\Exception $e) {
            error_log("ERROR en partial: " . $e->getMessage(), 3, SRC_PATH . "/../logs/app.log");
            throw $e;
        }
    }

    public static function setLayout($layout) {
        error_log("=== CAMBIO DE LAYOUT ===", 3, SRC_PATH . "/../logs/app.log");
        error_log("Cambiando layout de '" . self::$layout . "' a '" . $layout . "'", 3, SRC_PATH . "/../logs/app.log");
        error_log("Backtrace: " . print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), 3, SRC_PATH . "/../logs/app.log");
        self::$layout = $layout;
        error_log("Layout cambiado exitosamente", 3, SRC_PATH . "/../logs/app.log");
    }

    public static function getLayout() {
        return self::$layout;
    }
}