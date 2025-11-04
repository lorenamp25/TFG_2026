<?php
require_once __DIR__ . '/../models/Receta.php';

class RecetaController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /recetas
    public function index() {
        $model = new Receta($this->conn);
        $data = $model->getAll();
        echo json_encode($data);
    }

    // GET /recetas/{id}
    public function show($id) {
        $model = new Receta($this->conn);
        $receta = $model->getById($id);

        if ($receta) {
            echo json_encode($receta);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Receta no encontrada"]);
        }
    }

    // OPTIONS /recetas (CORS preflight)
    public function options() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        http_response_code(204);
        exit;
    }

    // POST /recetas
    public function create($input) {
        if (!isset($input['titulo']) || empty(trim($input['titulo']))) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'titulo' es obligatorio"]);
            return;
        }

        $model = new Receta($this->conn);
        $model->titulo = $input['titulo'];
        $model->descripcion = $input['descripcion'] ?? null;
        $model->tiempo_preparacion = $input['tiempo_preparacion'] ?? null;
        $model->dificultad = $input['dificultad'] ?? null;
        $model->categoria = $input['categoria'] ?? null;
        $model->imagen_url = $input['imagen_url'] ?? null;
        $model->usuario_id = $input['usuario_id'] ?? null;

        // relaciones
        $model->ingredientes = $input['ingredientes'] ?? [];
        $model->instrucciones = $input['instrucciones'] ?? [];

        $id = $model->create();
        if ($id) {
            http_response_code(201);
            echo json_encode(["message" => "Receta creada", "id" => $id]);
        } else {
            http_response_code(500);
            $err = $model->lastError ?? null;
            echo json_encode(["error" => "Error al crear la receta", "detail" => $err]);
        }
    }

    // PUT /recetas/{id}
    public function update($id, $input) {
        $model = new Receta($this->conn);
        $model->id = $id;
        $model->titulo = $input['titulo'] ?? null;
        $model->descripcion = $input['descripcion'] ?? null;
        $model->tiempo_preparacion = $input['tiempo_preparacion'] ?? null;
        $model->dificultad = $input['dificultad'] ?? null;
        $model->categoria = $input['categoria'] ?? null;
        $model->imagen_url = $input['imagen_url'] ?? null;
        $model->usuario_id = $input['usuario_id'] ?? null;

        $model->ingredientes = $input['ingredientes'] ?? [];
        $model->instrucciones = $input['instrucciones'] ?? [];

        if (!$model->titulo) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'titulo' es obligatorio"]);
            return;
        }

        if ($model->update()) {
            echo json_encode(["message" => "Receta actualizada"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // DELETE /recetas/{id}
    public function delete($id) {
        $model = new Receta($this->conn);
        if ($model->delete($id)) {
            echo json_encode(["message" => "Receta eliminada"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
