<?php
require_once __DIR__ . '/../controllers/RecetaController.php';

function registerRecetaRoutes($router, $db) {
    $controller = new RecetaController($db);

    $router->register('GET',    'recetas',           [$controller, 'index']);
    $router->register('GET',    'recetas/{id}',      [$controller, 'show']);
    $router->register('POST',   'recetas',           function() use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->create($input);
    });
    $router->register('PUT',    'recetas/{id}',      function($id) use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->update($id, $input);
    });
    $router->register('DELETE', 'recetas/{id}',      [$controller, 'delete']);
}
