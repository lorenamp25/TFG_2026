<?php
// Importa el archivo del modelo Ingrediente que maneja las consultas a la base de datos
require_once __DIR__ . '/../models/Ingrediente.php';

// Define el controlador para gestionar ingredientes
class IngredienteController {

    // Propiedad donde guardamos la conexión a la base de datos
    private $conn;

    // Constructor: recibe la conexión a la base de datos y la guarda
    public function __construct($db) {
        $this->conn = $db;
    }

    // ---------------------------------------------------------
    // GET /ingredientes → Devuelve todos los ingredientes
    // ---------------------------------------------------------
    public function index() {
        // Crea una instancia del modelo Ingrediente
        $model = new Ingrediente($this->conn);

        // Obtiene todos los registros de la tabla ingredientes
        $data = $model->getAll();

        // Devuelve los datos en formato JSON
        echo json_encode($data);
    }

    // ---------------------------------------------------------
    // GET /ingredientes/{id} → Devuelve un ingrediente por ID
    // ---------------------------------------------------------
    public function show($id) {
        // Crea instancia del modelo
        $model = new Ingrediente($this->conn);

        // Busca el ingrediente según su ID en la BD
        $item = $model->getById($id);

        // Si existe, lo devuelve como JSON
        if ($item) {
            echo json_encode($item);

        // Si no existe, responde con error 404
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Ingrediente no encontrado"]);
        }
    }

    // ---------------------------------------------------------
    // OPTIONS /ingredientes → Respuesta CORS (preflight)
    // ---------------------------------------------------------
    public function options() {
        // Permite solicitudes desde cualquier origen (CORS)
        header('Access-Control-Allow-Origin: *');

        // Permite métodos HTTP aceptados
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Permite los encabezados que podrá enviar el cliente
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Devuelve código 204 (sin contenido)
        http_response_code(204);

        // Detiene la ejecución de OPTIONS
        exit;
    }

    // ---------------------------------------------------------
    // POST /ingredientes → Crear un nuevo ingrediente
    // ---------------------------------------------------------
    public function create($input) {

        // Valida que el campo "nombre" exista y no esté vacío
        if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
            http_response_code(400); // Petición incorrecta (bad request)
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return; // Detiene la ejecución
        }

        // Crea una instancia del modelo Ingrediente
        $model = new Ingrediente($this->conn);

        // Asigna al modelo el nombre recibido desde el cliente
        $model->nombre = $input['nombre'];

        // Llama al método create() y obtiene el ID insertado
        $id = $model->create();

        // Si la inserción fue exitosa
        if ($id) {
            http_response_code(201); // 201 = recurso creado
            echo json_encode(["message" => "Ingrediente creado", "id" => $id]);

        // Si falló la creación
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["error" => "Error al crear el ingrediente"]);
        }
    }

    // ---------------------------------------------------------
    // PUT /ingredientes/{id} → Actualizar un ingrediente existente
    // ---------------------------------------------------------
    public function update($id, $input) {

        // Crea instancia del modelo
        $model = new Ingrediente($this->conn);

        // Asigna el ID del ingrediente a actualizar
        $model->id = $id;

        // Asigna el nombre recibido (puede ser null si no se envía)
        $model->nombre = $input['nombre'] ?? null;

        // Validación: el nombre no puede ser null o vacío
        if (!$model->nombre) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return;
        }

        // Si la actualización se realizó con éxito
        if ($model->update()) {
            echo json_encode(["message" => "Ingrediente actualizado"]);

        // Si no se encontró el ingrediente o hubo un error
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // ---------------------------------------------------------
    // DELETE /ingredientes/{id} → Eliminar un ingrediente
    // ---------------------------------------------------------
    public function delete($id) {

        // Crea instancia del modelo
        $model = new Ingrediente($this->conn);

        // Si se eliminó correctamente
        if ($model->delete($id)) {
            echo json_encode(["message" => "Ingrediente eliminado"]);

        // Si falló o no existe el ID
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
