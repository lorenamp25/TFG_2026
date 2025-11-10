<?php
class Router {
    private $routes = [];

    public function register($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    public function resolve($method, $uri) {
        $method = strtoupper($method);
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            $pattern = trim($route['pattern'], '/');

            // Replace route parameters of the form {name:regex} or {name} with a named capture group
            $patternRegex = preg_replace_callback('/\{(\w+)(?::([^}]+))?\}/', function($m) {
                $name = $m[1];
                $regex = isset($m[2]) && $m[2] !== '' ? $m[2] : '[^/]+';
                return '(?P<' . $name . '>' . $regex . ')';
            }, $pattern);

            // Use '#' as delimiter so route parameter regex that contains '@' or other
            // characters won't prematurely terminate the regex (e.g. emails)
            $patternRegex = "#^" . $patternRegex . "$#";

            if ($route['method'] === $method && preg_match($patternRegex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return call_user_func_array($route['callback'], $params);
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
    }
}
