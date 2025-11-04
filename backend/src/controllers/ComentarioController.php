<?php
require_once __DIR__ . '/../models/Comentario.php';

class ComentarioController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /comentarios
    public function index() {
        $model = new Comentario($this->conn);
        $data = $model->getAll();
        echo json_encode($data);
    }

    // GET /comentarios/{id}
    public function show($id) {
        $model = new Comentario($this->conn);
        $item = $model->getById($id);

        if ($item) {
            echo json_encode($item);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Comentario no encontrado"]);
        }
    }

    // OPTIONS /comentarios (CORS preflight)
    public function options() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        http_response_code(204);
        exit;
    }

    // POST /comentarios
    public function create($input) {
        // Basic validation (adjust as needed)
        if (!isset($input['receta_id']) || !isset($input['usuario_id']) || !isset($input['contenido'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campos obligatorios: receta_id, usuario_id, contenido"]);
            return;
        }

        $model = new Comentario($this->conn);
        $model->receta_id = $input['receta_id'];
        $model->usuario_id = $input['usuario_id'];
        $model->contenido = $input['contenido'];
        $model->puntuacion = $input['puntuacion'] ?? null;

        $id = $model->create();
        if ($id) {
            http_response_code(201);
            echo json_encode(["message" => "Comentario creado", "id" => $id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear el comentario"]);
        }
    }

    // PUT /comentarios/{id}
    public function update($id, $input) {
        $model = new Comentario($this->conn);
        $model->id = $id;
        $model->contenido = $input['contenido'] ?? null;
        $model->puntuacion = $input['puntuacion'] ?? null;

        if (!$model->contenido) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'contenido' es obligatorio"]);
            return;
        }

        if ($model->update()) {
            echo json_encode(["message" => "Comentario actualizado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // DELETE /comentarios/{id}
    public function delete($id) {
        $model = new Comentario($this->conn);
        if ($model->delete($id)) {
            echo json_encode(["message" => "Comentario eliminado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
