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
            $patternRegex = "@^" . preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $pattern) . "$@";

            if ($route['method'] === $method && preg_match($patternRegex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return call_user_func_array($route['callback'], $params);
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
    }
}
