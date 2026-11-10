<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RoleMiddleware;
use RuntimeException;

final class Router
{
    /** @var array<int, array{0: string, 1: string, 2: array{0: class-string, 1: string}, 3?: array}> */
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $csrfResult = (new CsrfMiddleware())->handle($request);
            if ($csrfResult instanceof Response) {
                return $csrfResult;
            }
        }

        foreach ($this->routes as $route) {
            [$method, $pattern, $handler] = $route;
            $options = $route[3] ?? [];

            if (strtoupper($method) !== $request->method()) {
                continue;
            }

            $params = $this->match($pattern, $request->path());
            if ($params === null) {
                continue;
            }

            $middlewareResponse = $this->runMiddlewares($request, $options['middleware'] ?? []);
            if ($middlewareResponse instanceof Response) {
                return $middlewareResponse;
            }

            return $this->invokeHandler($handler, $request, $params);
        }

        return Response::abort(404);
    }

    /**
     * @return list<string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        $normalizedPattern = '/' . trim($pattern, '/');
        if ($normalizedPattern !== '/') {
            $normalizedPattern = rtrim($normalizedPattern, '/');
        }

        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([^/]+)', $normalizedPattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        array_shift($matches);
        return array_values($matches);
    }

    private function runMiddlewares(Request $request, array $middlewares): ?Response
    {
        foreach ($middlewares as $middleware) {
            if ($middleware === 'auth:admin') {
                $result = (new AuthMiddleware())->handle($request);
                if ($result instanceof Response) {
                    return $result;
                }
                continue;
            }

            if (str_starts_with($middleware, 'role:')) {
                $allowedRoles = explode(',', substr($middleware, 5));
                $result = (new RoleMiddleware($allowedRoles))->handle($request);
                if ($result instanceof Response) {
                    return $result;
                }
            }
        }

        return null;
    }

    private function invokeHandler(array $handler, Request $request, array $params): Response
    {
        [$controllerClass, $action] = $handler;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException('Controller class not found: ' . $controllerClass);
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $action)) {
            throw new RuntimeException(sprintf('Controller action %s::%s not found.', $controllerClass, $action));
        }

        $result = $controller->$action($request, ...$params);
        if (!$result instanceof Response) {
            throw new RuntimeException('Controller action must return an App\Core\Response instance.');
        }

        return $result;
    }
}

