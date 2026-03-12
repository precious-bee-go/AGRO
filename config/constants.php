<?php
// System Constants
define('APP_NAME', 'PreshyMarketplace - Organic Agricultural Products');
// Dynamic APP_URL Detection
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Calculate base path from the current script
$script = $_SERVER['SCRIPT_NAME'];
$baseDir = str_replace('\\', '/', dirname(dirname($script)));
if ($baseDir === '/')
    $baseDir = '';
define('APP_URL', $protocol . "://" . $host . $baseDir);

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('UPLOADS_PATH', ROOT_PATH . '/assets/uploads');

// Business Logic Constants
define('BOOKING_PERCENTAGE', 0.20); // 20% advance payment
?>