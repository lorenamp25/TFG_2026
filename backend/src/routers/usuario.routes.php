<?php
require_once __DIR__ . '/../controllers/UsuarioController.php';

function registerUsuarioRoutes($router, $db) {
    $controller = new UsuarioController($db);

    $router->register('GET',    'usuarios',           [$controller, 'index']);
    $router->register('GET',    'usuarios/{id}',      [$controller, 'show']);
    $router->register('POST',   'usuarios',           function() use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->create($input);
    });
    $router->register('PUT',    'usuarios/{id}',      function($id) use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->update($id, $input);
    });
    $router->register('DELETE', 'usuarios/{id}',      [$controller, 'delete']);
}
