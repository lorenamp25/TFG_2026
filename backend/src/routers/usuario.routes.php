<?php
require_once __DIR__ . '/../controllers/UsuarioController.php';

function registerUsuarioRoutes($router, $db) {
    $controller = new UsuarioController($db);

    $router->register('GET',    'usuarios',           [$controller, 'index']);
    $router->register('GET', 'usuarios/{id:\d+}', function($id) use ($controller) {
        // id numérico
        $controller->showById($id);
    });
    // Allow common email characters (letters, digits, @, dot, underscore, dash, plus, percent)
    $router->register('GET', 'usuarios/{email:[A-Za-z0-9@._%+\-]+}', function($email) use ($controller) {
        // id string (slug, uuid, etc.)
        $controller->showByEmail($email);
    });

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
