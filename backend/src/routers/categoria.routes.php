<?php
require_once __DIR__ . '/../controllers/CategoriaController.php';

function registerCategoriaRoutes($router, $db) {
    $controller = new CategoriaController($db);

    $router->register('GET',    'categorias',           [$controller, 'index']);
    $router->register('GET',    'categorias/{id}',      [$controller, 'show']);
    $router->register('POST',   'categorias',           function() use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->create($input);
    });
    $router->register('PUT',    'categorias/{id}',      function($id) use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->update($id, $input);
    });
    $router->register('DELETE', 'categorias/{id}',      [$controller, 'delete']);
}
