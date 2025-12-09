<?php
// Importa el controlador de Recetas para poder usar su lógica
require_once __DIR__ . '/../controllers/RecetaController.php';

// Función que registra todas las rutas relacionadas con "recetas"
function registerRecetaRoutes($router, $db) {

    // Crea una instancia del controlador usando la conexión a la BD
    $controller = new RecetaController($db);

    // Ruta GET /recetas → devuelve todas las recetas
    $router->register('GET', 'recetas', [$controller, 'index']);

    // Ruta GET /recetas/{id} → devuelve una receta concreta por ID
    $router->register('GET', 'recetas/{id}', [$controller, 'show']);

    // Ruta POST /recetas → crear una receta nueva
    $router->register('POST', 'recetas', function() use ($controller) {
        // Obtiene el cuerpo JSON de la petición y lo transforma en array
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método create() del controlador
        $controller->create($input);
    });

    // Ruta PUT /recetas/{id} → actualizar receta existente
    $router->register('POST', 'recetas/{id}', function($id) use ($controller) {
        // Lee el JSON enviado para actualizar la receta
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método update() del controlador pasando ID + datos
        $controller->update($id, $input);
    });

    // Ruta DELETE /recetas/{id} → eliminar una receta por ID
    $router->register('DELETE', 'recetas/{id}', [$controller, 'delete']);
}
