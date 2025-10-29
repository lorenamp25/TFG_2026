<?php
require_once __DIR__ . '/../models/Mensaje.php';

class MensajeController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /mensajes
    public function index() {
        $model = new Mensaje($this->conn);
        $data = $model->getAll();
        echo json_encode($data);
    }

    // GET /mensajes/{id}
    public function show($id) {
        $model = new Mensaje($this->conn);
        $item = $model->getById($id);

        if ($item) {
            echo json_encode($item);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Mensaje no encontrado"]);
        }
    }

    // OPTIONS /mensajes (CORS preflight)
    public function options() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        http_response_code(204);
        exit;
    }

    // POST /mensajes
    public function create($input) {
        if (!isset($input['remitente']) || !isset($input['destinatario']) || !isset($input['contenido'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campos obligatorios: remitente, destinatario, contenido"]);
            return;
        }

        $model = new Mensaje($this->conn);
        $model->remitente = $input['remitente'];
        $model->destinatario = $input['destinatario'];
        $model->asunto = $input['asunto'] ?? null;
        $model->contenido = $input['contenido'];

        $id = $model->create();
        if ($id) {
            http_response_code(201);
            echo json_encode(["message" => "Mensaje creado", "id" => $id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear el mensaje"]);
        }
    }

    // PUT /mensajes/{id}
    public function update($id, $input) {
        $model = new Mensaje($this->conn);
        $model->id = $id;
        $model->contenido = $input['contenido'] ?? null;

        if (!$model->contenido) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'contenido' es obligatorio"]);
            return;
        }

        if ($model->update()) {
            echo json_encode(["message" => "Mensaje actualizado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // DELETE /mensajes/{id}
    public function delete($id) {
        $model = new Mensaje($this->conn);
        if ($model->delete($id)) {
            echo json_encode(["message" => "Mensaje eliminado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
