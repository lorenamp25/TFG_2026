<?php
// Importa el modelo Usuario que contiene la lógica de acceso a la base de datos
require_once __DIR__ . '/../models/Usuario.php';

// Controlador encargado de gestionar usuarios
class AuthController {

    // Propiedad donde se guardará la conexión a la base de datos
    private $conn;

    // Constructor: recibe el objeto de conexión y lo guarda en el controlador
    public function __construct($db) {
        $this->conn = $db;
    }

    
    // -----------------------------------------------------------
    // OPTIONS /usuarios → Respuesta CORS para preflight
    // -----------------------------------------------------------
    public function options() {
        // Permite solicitudes desde cualquier origen
        header('Access-Control-Allow-Origin: *');

        // Permite métodos HTTP especificados
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Permite cabeceras personalizadas
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Responde con 204 (sin contenido)
        http_response_code(204);

        // Termina la ejecución para OPTIONS
        exit;
    }

    // -----------------------------------------------------------
    // POST /login → Checkea las credenciales de login
    // -----------------------------------------------------------
    public function login($input) {

        // Validación básica: email y password son obligatorios
        if (!isset($input['email']) || !isset($input['password'])) {
            http_response_code(400); // Petición mal hecha
            echo json_encode(["error" => "Campos obligatorios: email, password"]);
            return;
        }

        $email = trim($input['email']);
        $password = $input['password'];

        try {
            // Preparar consulta SQL
            $query = "SELECT id, email, password FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);

            // Bind del parámetro
            $stmt->bindParam(':email', $email);

            // Ejecutar consulta
            $stmt->execute();

            // Obtener usuario
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no existe
            if (!$usuario) {
                http_response_code(401);
                echo json_encode(["error" => "Credenciales incorrectas"]);
                return;
            }

            // Validar contraseña usando password_verify
            if (!password_verify($password, $usuario['password'])) {
                http_response_code(401);
                echo json_encode(["error" => "Credenciales incorrectas"]);
                return;
            }

            $model = new Usuario($this->conn);

            // Llama al método getById para buscar por ID
            $usuarioCompleto = $model->getById($usuario['id']);

            http_response_code(200);
            echo json_encode([
                "message" => "Login exitoso",
                "usuario" => $usuarioCompleto
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
        }
    }
}