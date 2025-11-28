<?php
// Importa el controlador de Comentarios para poder usarlo en las rutas
require_once __DIR__ . '/../controllers/ComentarioController.php';

// Función que registra todas las rutas relacionadas con "comentarios"
function registerComentarioRoutes($router, $db) {

    // Crea una instancia del controlador y le pasa la conexión a la BD
    $controller = new ComentarioController($db);

    // Ruta GET /comentarios → devuelve todos los comentarios
    $router->register('GET', 'comentarios', [$controller, 'index']);

    // Ruta GET /comentarios/{id} → devuelve un comentario por su ID
    $router->register('GET', 'comentarios/{id}', [$controller, 'show']);

    // Ruta POST /comentarios → crear un nuevo comentario
    $router->register('POST', 'comentarios', function() use ($controller) {
        // Lee el cuerpo JSON enviado en la petición (php://input)
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método create() del controlador
        $controller->create($input);
    });

    // Ruta PUT /comentarios/{id} → actualizar un comentario existente
    $router->register('PUT', 'comentarios/{id}', function($id) use ($controller) {
        // Lee el JSON con los datos a actualizar
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método update() pasándole el ID y los datos
        $controller->update($id, $input);
    });

    // Ruta DELETE /comentarios/{id} → eliminar un comentario
    $router->register('DELETE', 'comentarios/{id}', [$controller, 'delete']);
}
