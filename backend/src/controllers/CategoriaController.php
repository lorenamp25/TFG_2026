<?php
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /categorias
    public function index() {
        $model = new Categoria($this->conn);
        $data = $model->getAll();
        echo json_encode($data);
    }

    // GET /categorias/{id}
    public function show($id) {
        $model = new Categoria($this->conn);
        $categoria = $model->getById($id);

        if ($categoria) {
            echo json_encode($categoria);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Categoría no encontrada"]);
        }
    }

    // OPTIONS /categorias (CORS preflight)
    public function options() {
        // Allow any origin (adjust to your needs)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        // No content for preflight
        http_response_code(204);
        exit;
    }

    // POST /categorias
    public function create($input) {
        if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return;
        }

        $model = new Categoria($this->conn);
        $model->nombre = $input['nombre'];
        $model->descripcion = $input['descripcion'];
        $model->icono = $input['icono'];

        $id = $model->create();
        if ($id) {
            http_response_code(201);
            echo json_encode(["message" => "Categoría creada", "id" => $id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear la categoría"]);
        }
    }

    // 🔹 PUT /categorias/{id}
    public function update($id, $input) {
        $model = new Categoria($this->conn);
        $model->id = $id;
        $model->nombre = $input['nombre'] ?? null;
        $model->descripcion = $input['descripcion'];
        $model->icono = $input['icono'];

        if (!$model->nombre) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return;
        }

        if ($model->update()) {
            echo json_encode(["message" => "Categoría actualizada"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // 🔹 DELETE /categorias/{id}
    public function delete($id) {
        $model = new Categoria($this->conn);
        if ($model->delete($id)) {
            echo json_encode(["message" => "Categoría eliminada"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
