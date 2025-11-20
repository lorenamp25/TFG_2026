<?php
// Importa el modelo Usuario que contiene la lógica de acceso a la base de datos
require_once __DIR__ . '/../models/Usuario.php';

// Controlador encargado de gestionar usuarios
class UsuarioController {

    // Propiedad donde se guardará la conexión a la base de datos
    private $conn;

    // Constructor: recibe el objeto de conexión y lo guarda en el controlador
    public function __construct($db) {
        $this->conn = $db;
    }

    // -----------------------------------------------------------
    // GET /usuarios → Obtener todos los usuarios
    // -----------------------------------------------------------
    public function index() {
        // Crea instancia del modelo Usuario
        $model = new Usuario($this->conn);

        // Llama a getAll() para obtener todos los registros
        $data = $model->getAll();

        // Devuelve la lista en formato JSON
        echo json_encode($data);
    }

    // -----------------------------------------------------------
    // GET /usuarios/{id} → Buscar usuario por ID
    // -----------------------------------------------------------
    public function showById($id) {
        // Instancia el modelo
        $model = new Usuario($this->conn);

        // Llama al método getById para buscar por ID
        $user = $model->getById($id);

        // Si existe lo devuelve
        if ($user) {
            echo json_encode($user);

        // Si no existe, error 404
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
        }
    }
    
    // -----------------------------------------------------------
    // GET /usuarios/email/{email} → Buscar usuario por email
    // -----------------------------------------------------------
    public function showByEmail($email) {
        // Instancia el modelo
        $model = new Usuario($this->conn);

        // Llama a getByMail() para obtener un usuario por email
        $user = $model->getByMail($email);

        // Si existe lo devuelve
        if ($user) {
            echo json_encode($user);

        // Si no se encontró el email
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
        }
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
    // POST /usuarios → Crear un nuevo usuario
    // -----------------------------------------------------------
    public function create($input) {

        // Validación básica: nickname, email y password son obligatorios
        if (!isset($input['nickname']) || !isset($input['email']) || !isset($input['password'])) {
            http_response_code(400); // Petición mal hecha
            echo json_encode(["error" => "Campos obligatorios: nickname, email, password"]);
            return;
        }

        // Instancia del modelo Usuario
        $model = new Usuario($this->conn);

        // Asignación de campos
        $model->nickname = $input['nickname'];
        $model->nombre = $input['nombre'] ?? null;
        $model->apellido = $input['apellido'] ?? null;
        $model->email = $input['email'];
        $model->es_admin = $input['es_admin'] ?? false;

        // Encripta la contraseña antes de guardarla (muy importante)
        $model->password = password_hash($input['password'], PASSWORD_DEFAULT);

        $model->fecha_nacimiento = $input['fecha_nacimiento'] ?? null;

        // Si no se especifica puntuación, se pone 0
        $model->puntuacion = $input['puntuacion'] ?? 0;

        // Inserta el usuario y recibe el ID
        $id = $model->create();

        // Si se creó correctamente
        if ($id) {
            http_response_code(201); // Recurso creado
            echo json_encode(["message" => "Usuario creado", "id" => $id]);

        // Si hubo un error en la base de datos
        } else {
            http_response_code(500); // Error interno
            echo json_encode(["error" => "Error al crear usuario"]);
        }
    }

    // -----------------------------------------------------------
    // PUT /usuarios/{id} → Actualizar un usuario existente
    // -----------------------------------------------------------
    public function update($id, $input) {

        // Instancia el modelo
        $model = new Usuario($this->conn);

        // Asigna el ID del usuario a actualizar
        $model->id = $id;

        // Asigna los datos recibidos (null si no se pasan)
        $model->nickname = $input['nickname'] ?? null;
        $model->nombre = $input['nombre'] ?? null;
        $model->apellido = $input['apellido'] ?? null;
        $model->email = $input['email'] ?? null;
        $model->es_admin = $input['es_admin'] ?? false;

        // Si el usuario envió una nueva contraseña, se hashea
        if (isset($input['password'])) {
            $model->password = password_hash($input['password'], PASSWORD_DEFAULT);
        }

        $model->fecha_nacimiento = $input['fecha_nacimiento'] ?? null;

        // Actualiza puntuación (o 0 si no viene)
        $model->puntuacion = $input['puntuacion'] ?? 0;

        // Llama al método update() y evalúa el resultado
        if ($model->update()) {
            echo json_encode(["message" => "Usuario actualizado"]);
        } else {
            http_response_code(404); // No encontrado o error
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // -----------------------------------------------------------
    // DELETE /usuarios/{id} → Eliminar un usuario
    // -----------------------------------------------------------
    public function delete($id) {

        // Instancia el modelo
        $model = new Usuario($this->conn);

        // Intenta eliminarlo
        if ($model->delete($id)) {
            echo json_encode(["message" => "Usuario eliminado"]);

        // Si no existe o hubo un error
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
