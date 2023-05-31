<?php

class Router {
    private $connection;
    private $authMiddleware;
    private $routes = [];

    public function __construct($connection, $authMiddleware) {
        $this->connection = $connection;
        $this->authMiddleware = $authMiddleware;
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        $route = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];

        // Replace dynamic segments with regular expressions
        $route['regex'] = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)', $path);

        $this->routes[] = $route;
    }

    public function handleRequest($method, $uri) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $matches = [];
                if (preg_match('#^' . $route['regex'] . '$#', $uri, $matches)) {
                    array_shift($matches); // Remove the full match from the matches

                    return $this->invokeHandler($route['handler'], $matches, $_REQUEST);
                }
            }
        }

        // Route not found
        // You can handle this case as per your requirement
        $this->handleError(404, 'Route not found');
    }

    private function invokeHandler($handler, $matches, $request) {
        // Split the handler into controller and method
        [$controller, $method] = explode('@', $handler);

        // Check if the controller class exists
        if (class_exists($controller)) {
            $instance = new $controller($this->connection, $this->authMiddleware);

            // Check if the method exists in the controller
            if (method_exists($instance, $method)) {
                $arguments = array_merge([$matches], [$request]);
                return call_user_func_array([$instance, $method], $arguments);
            }
        }

        // Handler or controller not found
        // You can handle this case as per your requirement
        $this->handleError(404, 'Handler or controller not found');
    }

    private function handleError($statusCode, $message) {
        http_response_code($statusCode);
        echo json_encode(['message' => $message]);
    }
}
