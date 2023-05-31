<?php

$router->post('/login', 'UserController@login');
$router->get('/users', 'UserController@getUsers');
$router->post('/users', 'UserController@createUser');
$router->get('/users/{id}', 'UserController@getUser');
$router->post('/users/{id}', 'UserController@updateUser');
$router->delete('/users/{id}', 'UserController@deleteUser');