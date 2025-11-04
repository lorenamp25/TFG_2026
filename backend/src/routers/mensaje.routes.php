<?php
require_once __DIR__ . '/../controllers/MensajeController.php';

function registerMensajeRoutes($router, $db) {
    $controller = new MensajeController($db);

    $router->register('GET',    'mensajes',           [$controller, 'index']);
    $router->register('GET',    'mensajes/{id}',      [$controller, 'show']);
    $router->register('POST',   'mensajes',           function() use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->create($input);
    });
    $router->register('PUT',    'mensajes/{id}',      function($id) use ($controller) {
        $input = json_decode(file_get_contents("php://input"), true);
        $controller->update($id, $input);
    });
    $router->register('DELETE', 'mensajes/{id}',      [$controller, 'delete']);
}
