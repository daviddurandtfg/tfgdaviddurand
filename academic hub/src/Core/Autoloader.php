<?php
namespace Softhub\Core;
//este archivo es src/Core/Autoloader.php
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Verificar si la clase pertenece a nuestro namespace
            $prefix = 'Softhub\\';
            $base_dir = dirname(__DIR__) . '/';

            // ¿La clase usa el prefijo?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            // Obtener el nombre relativo de la clase
            $relative_class = substr($class, $len);

            // Reemplazar el prefijo del namespace con el directorio base
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // Si el archivo existe, cargarlo
            if (file_exists($file)) {
                require $file;
            }
        });
    }
}