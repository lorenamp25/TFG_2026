<?php
// image.routes.php
require_once __DIR__ . '/../controllers/ImageController.php';

function registerImageRoutes($router)
{
    $controller = new ImageController();

    // Ruta para servir imágenes
    $router->register('GET', 'images', [$controller, 'serve']);

    // Ruta OPTIONS para CORS
    $router->register('OPTIONS', 'images', [$controller, 'options']);
}
