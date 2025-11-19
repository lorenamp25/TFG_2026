<?php
// Importa el controlador de Ingredientes para poder usarlo en estas rutas
require_once __DIR__ . '/../controllers/IngredienteController.php';

// Función que registra las rutas relacionadas con "ingredientes"
function registerIngredienteRoutes($router, $db) {

    // Crea una instancia del controlador pasándole la conexión a la BD
    $controller = new IngredienteController($db);

    // Ruta GET /ingredientes → obtener todos los ingredientes
    $router->register('GET', 'ingredientes', [$controller, 'index']);

    // Ruta GET /ingredientes/{id} → obtener un ingrediente por ID
    $router->register('GET', 'ingredientes/{id}', [$controller, 'show']);

    // Ruta POST /ingredientes → crear un ingrediente nuevo
    $router->register('POST', 'ingredientes', function() use ($controller) {
        // Lee el JSON del cuerpo de la petición
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método create() del controlador
        $controller->create($input);
    });

    // Ruta PUT /ingredientes/{id} → actualizar un ingrediente existente
    $router->register('PUT', 'ingredientes/{id}', function($id) use ($controller) {
        // Lee el JSON que contiene los datos modificados
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método update() pasándole ID + datos
        $controller->update($id, $input);
    });

    // Ruta DELETE /ingredientes/{id} → eliminar un ingrediente por ID
    $router->register('DELETE', 'ingredientes/{id}', [$controller, 'delete']);
}
