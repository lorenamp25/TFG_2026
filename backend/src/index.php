<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle CORS preflight requests quickly to avoid routing them and returning 404
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	// Already sent CORS headers above; just return No Content
	http_response_code(204);
	exit;
}

require_once __DIR__ . '/db/Database.php';
require_once __DIR__ . '/routers/Router.php';

// Conexión DB
$database = new Database();
$db = $database->getConnection();

// Crear tablas automáticamente al iniciar, a menos que DB_INIT=0
$dbInit = getenv('DB_INIT');
if ($db && $dbInit !== '0' && strtolower($dbInit) !== 'false') {
	// createTables devuelve true/false
	$database->createTables();
}

// Router principal
$router = new Router();

// Registrar rutas de cada módulo
require_once __DIR__ . '/routers/categoria.routes.php';



registerCategoriaRoutes($router, $db);

require_once __DIR__ . '/routers/ingrediente.routes.php';
require_once __DIR__ . '/routers/comentario.routes.php';
require_once __DIR__ . '/routers/mensaje.routes.php';

registerIngredienteRoutes($router, $db);
registerComentarioRoutes($router, $db);
registerMensajeRoutes($router, $db);



// Resolver la solicitud
$router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
