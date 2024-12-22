<?php
/**
config/config.php
 * Archivo de configuración principal
 *
 * Contiene configuraciones globales del sistema
 */

// Configuración de errores
define('DEV_MODE', true);

if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Definir rutas base
define('BASE_PATH', dirname(__DIR__));
define('SRC_PATH', BASE_PATH . '/src');
define('BASE_URL', '/softhub');
define('LOG_PATH', BASE_PATH . '/logs');

// Configuración de base de datos
define('DB_HOST', 'softhub-db.c7gskoyqo5fx.us-east-1.rds.amazonaws.com');
define('DB_NAME', 'softhub_dev');
define('DB_USER', 'admin');
define('DB_PASS', 'Lorosda_2024');

// Asegurar que existe el directorio de logs
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0777, true);
}

// Función de logging
function app_log($message, $type = 'INFO') {
    $log_file = LOG_PATH . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp][$type] $message" . PHP_EOL;
    file_put_contents($log_file, $log_message, FILE_APPEND);
}