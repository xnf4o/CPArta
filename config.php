<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'clean');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Token Secret Configuration
define('TOKEN_SECRET', 'your_token_secret_key');

// Establish Database Connection
$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check Connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Set Character Set
$connection->set_charset("utf8");