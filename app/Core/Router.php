<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $currentRoute = null;

    public function add($method, $path, $controller, $action, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch() {
        $url = $this->parseUrl();
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            $pattern = $this->convertRouteToRegex($route['path']);
            
            if (preg_match($pattern, $url, $matches) && $route['method'] === $method) {
                array_shift($matches); // Remove the full match
                
                // Run middleware
                foreach ($route['middleware'] as $middleware) {
                    $instance = new $middleware();
                    if (!$instance->handle()) {
                        return false;
                    }
                }

                $controller = new $route['controller']();
                call_user_func_array([$controller, $route['action']], $matches);
                return true;
            }
        }

        // No route found
        header("HTTP/1.0 404 Not Found");
        require_once __DIR__ . '/../../app/Views/errors/404.php';
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return rtrim(filter_var($_GET['url'], FILTER_SANITIZE_URL), '/');
        }
        return '';
    }

    private function convertRouteToRegex($route) {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $route);
        return "/^" . str_replace('/', '\/', $pattern) . "$/";
    }
}
