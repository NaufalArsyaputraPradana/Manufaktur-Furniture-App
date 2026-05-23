<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tentukan path ke folder project Laravel Anda
$projectPath = __DIR__ . '/../repositories/Manufaktur-Furniture-App';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $projectPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $projectPath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $projectPath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());