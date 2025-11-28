<?php
// Clase Router encargada de registrar rutas y resolverlas
class Router {

    // Aquí se almacenarán todas las rutas registradas
    private $routes = [];

    // Registra una nueva ruta en el array $routes
    public function register($method, $pattern, $callback) {
        // Guarda la ruta con su método HTTP, el patrón y el callback asociado
        $this->routes[] = [
            'method' => strtoupper($method),  // Normaliza el método HTTP en mayúsculas
            'pattern' => $pattern,            // Patrón de la ruta (ej: "recetas/{id}")
            'callback' => $callback           // Función o método que debe ejecutarse
        ];
    }

    // Resuelve una petición buscando coincidencias con las rutas registradas
    public function resolve($method, $uri) {

        // Normaliza el método HTTP
        $method = strtoupper($method);

        // Obtiene solo la parte del path del URI y elimina barras sobrantes
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        // Recorre todas las rutas registradas
        foreach ($this->routes as $route) {

            // Normaliza el patrón de la ruta registrada
            $pattern = trim($route['pattern'], '/');

            /**
             * Convierte patrones como:
             *   {id} → (?P<id>[^/]+)
             *   {email:.+@.+} → (?P<email>.+@.+)
             * usando preg_replace_callback
             */
            $patternRegex = preg_replace_callback(
                '/\{(\w+)(?::([^}]+))?\}/',        // busca {nombre} o {nombre:regex}
                function($m) {
                    $name = $m[1];                // nombre del parámetro
                    $regex = isset($m[2]) && $m[2] !== '' ? $m[2] : '[^/]+'; 
                    // regex por defecto: todo menos "/"
                    return '(?P<' . $name . '>' . $regex . ')';
                },
                $pattern
            );

            /**
             * Construimos una expresión regular completa usando # como delimitador,
             * para evitar conflictos con caracteres como @ en emails.
             */
            $patternRegex = "#^" . $patternRegex . "$#";

            // Si coincide el método HTTP y el patrón concuerda con el URI...
            if ($route['method'] === $method && preg_match($patternRegex, $uri, $matches)) {

                // Filtra solo los parámetros capturados por nombre
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Ejecuta el callback asociado, pasándole los parámetros capturados
                return call_user_func_array($route['callback'], $params);
            }
        }

        // Si ninguna ruta coincide → 404 Not Found
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
    }
}
