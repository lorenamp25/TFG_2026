<?php
require_once __DIR__ . '/../controllers/ComentarioController.php';

function registerComentarioRoutes($router, $db) {
    $controller = new ComentarioController($db);

    $router->register('GET',    'comentarios',           [$controller, 'index']);
    $router->register('GET',    'comentarios/{id}',      [$controller, 'show']);
    $router->register('POST',   'comentarios',           function() use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->create($input);
    });
    $router->register('PUT',    'comentarios/{id}',      function($id) use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->update($id, $input);
    });
    $router->register('DELETE', 'comentarios/{id}',      [$controller, 'delete']);
}
