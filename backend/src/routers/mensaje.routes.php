<?php
// Importa el controlador de Mensajes para poder manejar la lógica
require_once __DIR__ . '/../controllers/MensajeController.php';

// Función que registra todas las rutas relacionadas con "mensajes"
function registerMensajeRoutes($router, $db) {

    // Crea una instancia del controlador usando la conexión a la base de datos
    $controller = new MensajeController($db);

    // Ruta GET /mensajes → devuelve todos los mensajes
    $router->register('GET', 'mensajes', [$controller, 'index']);

    // Ruta GET /mensajes/{id} → devuelve un mensaje concreto por su ID
    $router->register('GET', 'mensajes/{id}', [$controller, 'show']);

    // Ruta POST /mensajes → crear un mensaje nuevo
    $router->register('POST', 'mensajes', function() use ($controller) {
        // Lee el cuerpo de la petición (JSON) y lo convierte en array
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método create() del controlador
        $controller->create($input);
    });

    // Ruta PUT /mensajes/{id} → actualizar un mensaje existente
    $router->register('PUT', 'mensajes/{id}', function($id) use ($controller) {
        // Lee el JSON enviado para actualizar el mensaje
        $input = json_decode(file_get_contents("php://input"), true);
        // Llama al método update() pasándole el ID + los datos
        $controller->update($id, $input);
    });

    // Ruta DELETE /mensajes/{id} → eliminar un mensaje específico
    $router->register('DELETE', 'mensajes/{id}', [$controller, 'delete']);
}
