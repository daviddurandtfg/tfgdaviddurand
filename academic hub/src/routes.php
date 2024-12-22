<?php
// src/routes.php

// Rutas públicas
$router->add('/', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);

// Rutas protegidas - Usuario
$router->add('dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
$router->add('profile', ['controller' => 'User', 'action' => 'profile']);

// Rutas protegidas - Admin
$router->add('admin', ['controller' => 'Admin', 'action' => 'index']);
$router->add('admin/users', ['controller' => 'Admin', 'action' => 'users']);

// Rutas de error
$router->add('404', ['controller' => 'Error', 'action' => 'notFound']);