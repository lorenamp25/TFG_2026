<?php
// Importa el controlador de Usuario para manejar la lógica
require_once __DIR__ . '/../controllers/UsuarioController.php';

// Función que registra todas las rutas relacionadas con usuarios
function registerUsuarioRoutes($router, $db) {

    // Crea una instancia del controlador de usuarios con la conexión a BD
    $controller = new UsuarioController($db);

    // Ruta GET /usuarios → devuelve todos los usuarios
    $router->register('GET', 'usuarios', [$controller, 'index']);

    // Ruta GET /usuarios/{id} → busca un usuario por ID numérico
    $router->register('GET', 'usuarios/{id:\d+}', function($id) use ($controller) {
        // Llama al método que busca usuario por ID
        $controller->showById($id);
    });

    /**
     * Ruta GET /usuarios/{email}
     * Esta ruta acepta emails como parámetro gracias al regex:
     * [A-Za-z0-9@._%+\-]+  → permite letras, números, ., _, %, +, -, y @
     */
    $router->register(
        'GET',
        'usuarios/{email:[A-Za-z0-9@._%+\-]+}',
        function($email) use ($controller) {
            // Llama al método que busca usuario por email
            $controller->showByEmail($email);
        }
    );

    // Ruta POST /usuarios → crear usuario nuevo
    $router->register('POST', 'usuarios', function() use ($controller) {
        // Lee el cuerpo JSON de la petición y lo convierte a array
        $input = json_decode(file_get_contents("php://input"), true);
        // Crea el usuario
        $controller->create($input);
    });

    // Ruta PUT /usuarios/{id} → actualizar un usuario existente
    $router->register('PUT', 'usuarios/{id}', function($id) use ($controller) {
        // Obtiene datos JSON para actualizar
        $input = json_decode(file_get_contents("php://input"), true);
        // Ejecuta la actualización
        $controller->update($id, $input);
    });

    // Ruta DELETE /usuarios/{id} → eliminar un usuario
    $router->register('DELETE', 'usuarios/{id}', [$controller, 'delete']);
}
