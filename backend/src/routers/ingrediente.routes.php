<?php
require_once __DIR__ . '/../controllers/IngredienteController.php';

function registerIngredienteRoutes($router, $db) {
    $controller = new IngredienteController($db);

    $router->register('GET',    'ingredientes',           [$controller, 'index']);
    $router->register('GET',    'ingredientes/{id}',      [$controller, 'show']);
    $router->register('POST',   'ingredientes',           function() use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->create($input);
    });
    $router->register('PUT',    'ingredientes/{id}',      function($id) use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->update($id, $input);
    });
    $router->register('DELETE', 'ingredientes/{id}',      [$controller, 'delete']);
}
