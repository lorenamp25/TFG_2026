<?php
// Incluye el controlador de Categoría para poder usarlo en las rutas
require_once __DIR__ . '/../controllers/CategoriaController.php';

// Función que registra todas las rutas relacionadas con "categorias"
function registerCategoriaRoutes($router, $db) {

    // Crea una instancia del controlador y le pasa la conexión a la BD
    $controller = new CategoriaController($db);

    // Ruta GET /categorias → devuelve todas las categorías
    $router->register('GET', 'categorias', [$controller, 'index']);

    // Ruta GET /categorias/{id} → devuelve una categoría por su ID
    $router->register('GET', 'categorias/{id}', [$controller, 'show']);

    // Ruta POST /categorias → crear nueva categoría
    $router->register('POST', 'categorias', function() use ($controller) {
        // Lee el cuerpo JSON de la petición
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método create() del controlador
        $controller->create($input);
    });

    // Ruta PUT /categorias/{id} → actualizar categoría existente
    $router->register('PUT', 'categorias/{id}', function($id) use ($controller) {
        // Lee el cuerpo JSON
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método update() pasando el id y los datos
        $controller->update($id, $input);
    });

    // Ruta DELETE /categorias/{id} → eliminar categoría
    $router->register('DELETE', 'categorias/{id}', [$controller, 'delete']);
}
