<?php

require 'config.php';
require 'Router.php';
require 'middlewares/AuthMiddleware.php';
require 'controllers/UserController.php';
require 'models/User.php';

// Database Connection
$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($connection->connect_errno) {
    die("Failed to connect to MySQL: " . $connection->connect_error);
}

// Auth Middleware
$authMiddleware = new AuthMiddleware($connection);

// Router
$router = new Router($connection, $authMiddleware);
require 'routes.php';

// Handle the request
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$router->handleRequest($method, $path);