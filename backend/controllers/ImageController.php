<?php
// ImageController.php
class ImageController
{
    public function serve()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        // Obtener el path del query parameter
        $imagePath = $_GET['path'] ?? '';

        if (empty($imagePath)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Path de imagen requerido"]);
            return;
        }

        // Validar seguridad - prevenir path traversal
        if (strpos($imagePath, '..') !== false || strpos($imagePath, '//') !== false) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Path no permitido"]);
            return;
        }

        // Ruta completa al archivo
        $fullPath = __DIR__ . '/../' . $imagePath;

        // Verificar que el archivo existe
        if (!file_exists($fullPath)) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Imagen no encontrada"]);
            return;
        }

        // Verificar que es realmente un archivo de imagen
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = mime_content_type($fullPath);

        if (!in_array($mimeType, $allowedTypes)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Tipo de archivo no permitido"]);
            return;
        }

        // Servir la imagen
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: public, max-age=86400'); // Cache de 1 día

        readfile($fullPath);
        exit;
    }

    public function options()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        http_response_code(204);
        exit;
    }
}
