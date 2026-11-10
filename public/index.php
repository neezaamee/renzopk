<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

$vendorAutoload = BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_file($vendorAutoload)) {
    require $vendorAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file = BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

        if (is_file($file)) {
            require $file;
        }
    });

    require BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Support' . DIRECTORY_SEPARATOR . 'helpers.php';
}

Env::load(BASE_PATH);
date_default_timezone_set((string) Env::get('APP_TIMEZONE', 'UTC'));
Session::start();

try {
    $routes = require BASE_PATH . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php';
    $request = Request::capture();
    $response = (new Router($routes))->dispatch($request);
    $response->send();
} catch (Throwable $e) {
    error_log(sprintf('[Renzo] %s in %s:%d', $e->getMessage(), $e->getFile(), $e->getLine()));

    $debug = Env::get('APP_DEBUG', false) === true;
    $message = $debug
        ? sprintf('%s (%s:%d)', $e->getMessage(), basename($e->getFile()), $e->getLine())
        : 'An unexpected error occurred. Please try again shortly.';

    Response::abort(500, $message)->send();
}

