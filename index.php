<?php

use Model\Database;
use Model\Router;

error_reporting(E_ALL);
ini_set( 'display_errors','1');
session_start();

include 'src/Autoloader.php';

$router = new Router();
$route = $router->getController($_SERVER['REQUEST_URI']);
try {
    $controllerName =  'Controller\\' . $route['controller'];
    $controllerAction = $route['action'];
    $controller = new $controllerName();
    $controller->$controllerAction();
} catch (Exception $ex) {
    echo $ex->getMessage();
}

$db = Database::getInstance();
$db->close();
