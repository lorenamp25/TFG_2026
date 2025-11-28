<?php
// Importa el controlador de Auth para manejar la lógica
require_once __DIR__ . '/../controllers/AuthController.php';

// Función que registra todas las rutas relacionadas con usuarios
function registerAuthRoutes($router, $db)
{

    // Crea una instancia del controlador de usuarios con la conexión a BD
    $controller = new AuthController($db);

    // Ruta POST /usuarios → crear usuario nuevo
    $router->register('POST', 'login', function () use ($controller) {
        // Lee el cuerpo JSON de la petición y lo convierte a array
        $input = json_decode(file_get_contents("php://input"), true);
        // Crea el usuario
        $controller->login($input);
    });

    // Ruta POST /registrar → crear usuario nuevo
    $router->register('POST', 'registrar', function () use ($controller) {
        // Lee el cuerpo JSON de la petición y lo convierte a array
        $input = json_decode(file_get_contents("php://input"), true);
        // Crea el usuario
        $controller->register($input);
    });
}
