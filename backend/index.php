<?php
header('Content-Type: application/json; charset=utf-8');
// Activa la visualización de errores (útil en desarrollo)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Indica que todas las respuestas serán JSON
header("Content-Type: application/json");

// Permite acceso desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");

// Permite estos métodos HTTP
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Permite enviar cabeceras personalizadas como Content-Type
header("Access-Control-Allow-Headers: Content-Type");

// Si la petición es OPTIONS → es una pre-solicitud CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Se devuelven solo los headers CORS y un 204 sin contenido
    http_response_code(204);
    exit;
}

// Importa la clase Database para manejar la conexión
require_once __DIR__ . '/db/Database.php';

// Importa el sistema de enrutador
require_once __DIR__ . '/routers/Router.php';

// --- Conexión con la base de datos ---
$database = new Database();
$db = $database->getConnection();

// Comprobamos si hay que crear tablas automáticamente
$dbInit = getenv('DB_INIT');   // lee la variable de entorno DB_INIT

// Si la base de datos existe y no se ha desactivado DB_INIT
if ($db && $dbInit !== '0' && strtolower($dbInit) !== 'false') {
    // Ejecuta createTables() para crear todas las tablas necesarias
    $database->createTables();
}

// Para activar datos de prueba, puedes descomentar esta línea:
$database->createTables();
$database->crearDatosDummy();

// --- Crear el router principal ---
$router = new Router();

// Importa todos los archivos de rutas (cada módulo tiene el suyo)
require_once __DIR__ . '/routers/auth.routes.php';
require_once __DIR__ . '/routers/categoria.routes.php';
require_once __DIR__ . '/routers/comentario.routes.php';
require_once __DIR__ . '/routers/image.routes.php';
require_once __DIR__ . '/routers/ingrediente.routes.php';
require_once __DIR__ . '/routers/mensaje.routes.php';
require_once __DIR__ . '/routers/receta.routes.php';
require_once __DIR__ . '/routers/usuario.routes.php';

// Registra las rutas en el router, enviándole también la conexión $db
registerAuthRoutes($router, $db);
registerCategoriaRoutes($router, $db);
registerComentarioRoutes($router, $db);
registerImageRoutes($router);
registerIngredienteRoutes($router, $db);
registerMensajeRoutes($router, $db);
registerRecetaRoutes($router, $db);
registerUsuarioRoutes($router, $db);

// Finalmente, procesa la petición actual
// Analiza el método (GET, POST, PUT, DELETE...) y la URL solicitada
$router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
