<?php
require_once __DIR__ . '/../models/Ingrediente.php';

class IngredienteController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /ingredientes
    public function index() {
        $model = new Ingrediente($this->conn);
        $data = $model->getAll();
        echo json_encode($data);
    }

    // GET /ingredientes/{id}
    public function show($id) {
        $model = new Ingrediente($this->conn);
        $item = $model->getById($id);

        if ($item) {
            echo json_encode($item);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Ingrediente no encontrado"]);
        }
    }

    // OPTIONS /ingredientes (CORS preflight)
    public function options() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        http_response_code(204);
        exit;
    }

    // POST /ingredientes
    public function create($input) {
        if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return;
        }

        $model = new Ingrediente($this->conn);
        $model->nombre = $input['nombre'];

        $id = $model->create();
        if ($id) {
            http_response_code(201);
            echo json_encode(["message" => "Ingrediente creado", "id" => $id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear el ingrediente"]);
        }
    }

    // PUT /ingredientes/{id}
    public function update($id, $input) {
        $model = new Ingrediente($this->conn);
        $model->id = $id;
        $model->nombre = $input['nombre'] ?? null;

        if (!$model->nombre) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre' es obligatorio"]);
            return;
        }

        if ($model->update()) {
            echo json_encode(["message" => "Ingrediente actualizado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // DELETE /ingredientes/{id}
    public function delete($id) {
        $model = new Ingrediente($this->conn);
        if ($model->delete($id)) {
            echo json_encode(["message" => "Ingrediente eliminado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
