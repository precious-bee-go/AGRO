<?php
// Main Entry Point
session_start();

// Load Config
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Basic Routing
$route = $_GET['url'] ?? 'home';
$routeParts = explode('/', $route);

// For now, let's just handle Auth manually to get it running
// We can expand this to a full Router class later
$controllerName = 'Controllers\\' . ucfirst($routeParts[0]) . 'Controller';
$methodName = $routeParts[1] ?? 'index';

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        $controller->$methodName();
    } else {
        die("Method $methodName not found in $controllerName");
    }
} else {
    // Default Home Route
    $home = new \Controllers\HomeController();
    $home->index();
}
?>