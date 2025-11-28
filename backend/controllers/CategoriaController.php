<?php
// Carga el archivo del modelo Categoria, que maneja la lógica de base de datos
require_once __DIR__ . '/../models/Categoria.php';

// Define la clase del controlador para categorías
class CategoriaController {

    // Propiedad privada donde se guardará la conexión a la base de datos
    private $conn;

    // Constructor: recibe el objeto de conexión a la base de datos y lo guarda en $conn
    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /categorias → Devuelve todas las categorías registradas
    public function index() {
        // Crea una instancia del modelo Categoria usando la conexión a la base de datos
        $model = new Categoria($this->conn);

        // Llama al método getAll() del modelo, que obtiene todas las categorías
        $data = $model->getAll();

        // Devuelve las categorías en formato JSON
        echo json_encode($data);
    }

    // GET /categorias/{id} → Devuelve una categoría específica según el ID
    public function show($id) {
        // Instancia el modelo Categoria
        $model = new Categoria($this->conn);

        // Busca la categoría por su ID llamando al método getById()
        $categoria = $model->getById($id);

        // Si la categoría existe, la devuelve en JSON
        if ($categoria) {
            echo json_encode($categoria);

        // Si no existe, devuelve un error 404
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Categoría no encontrada"]);
        }
    }

    // OPTIONS /categorias → Respuesta para peticiones CORS (preflight)
    public function options() {
        // Permite peticiones desde cualquier origen (CORS)
        header('Access-Control-Allow-Origin: *');

        // Permite los métodos HTTP GET, POST, PUT, DELETE y OPTIONS
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Permite estos headers en las peticiones
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Responde con el código 204 (sin contenido)
        http_response_code(204);

        // Finaliza la ejecución aquí
        exit;
    }

    // POST /categorias → Crear una nueva categoría
    public function create($input) {

        // Verifica si el campo 'nombre' existe y no está vacío
        if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
            http_response_code(400); // Error 400 = petición incorrecta
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return; // Detiene aquí la ejecución
        }

        // Crea instancia del modelo Categoria
        $model = new Categoria($this->conn);

        // Asigna valores del input al modelo
        $model->nombre = $input['nombre'];
        $model->descripcion = $input['descripcion'];
        $model->icono = $input['icono'];

        // Llama al método create() y guarda el ID generado
        $id = $model->create();

        // Si la creación fue exitosa
        if ($id) {
            http_response_code(201); // 201 = creado exitosamente
            echo json_encode(["message" => "Categoría creada", "id" => $id]);

        // Si hubo un error al crear
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["error" => "Error al crear la categoría"]);
        }
    }

    // PUT /categorias/{id} → Actualizar una categoría existente
    public function update($id, $input) {

        // Instancia del modelo Categoria
        $model = new Categoria($this->conn);

        // Asigna el ID de la categoría a actualizar
        $model->id = $id;

        // Asigna los campos recibidos
        $model->nombre = $input['nombre'] ?? null; // Si no existe, será null
        $model->descripcion = $input['descripcion'];
        $model->icono = $input['icono'];

        // Validación: el nombre es obligatorio
        if (!$model->nombre) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return;
        }

        // Si la actualización tuvo éxito
        if ($model->update()) {
            echo json_encode(["message" => "Categoría actualizada"]);

        // Si la actualización falló (categoria no encontrada o error)
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // DELETE /categorias/{id} → Eliminar una categoría
    public function delete($id) {
        // Crea instancia del modelo Categoria
        $model = new Categoria($this->conn);

        // Si se pudo eliminar correctamente
        if ($model->delete($id)) {
            echo json_encode(["message" => "Categoría eliminada"]);

        // Si no se encontró o no se pudo eliminar
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
