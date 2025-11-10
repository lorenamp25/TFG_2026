<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET /usuarios
    public function index() {
        $model = new Usuario($this->conn);
        $data = $model->getAll();
        echo json_encode($data);
    }

    // GET /usuarios/{id}
    public function showById($id) {
        $model = new Usuario($this->conn);
        $user = $model->getById($id);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
        }
    }
    
    // GET /usuarios/{id}
    public function showByEmail($email) {
        $model = new Usuario($this->conn);
        $user = $model->getByMail($email);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
        }
    }

    // OPTIONS /usuarios
    public function options() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        http_response_code(204);
        exit;
    }

    // POST /usuarios
    public function create($input) {
        if (!isset($input['nickname']) || !isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campos obligatorios: nickname, email, password"]);
            return;
        }

        $model = new Usuario($this->conn);
        $model->nickname = $input['nickname'];
        $model->nombre = $input['nombre'] ?? null;
        $model->apellido = $input['apellido'] ?? null;
        $model->email = $input['email'];
        // En producción, siempre hashea la contraseña antes de guardar
        $model->password = password_hash($input['password'], PASSWORD_DEFAULT);
        $model->fecha_nacimiento = $input['fecha_nacimiento'] ?? null;
        $model->puntuacion = $input['puntuacion'] ?? 0;

        $id = $model->create();
        if ($id) {
            http_response_code(201);
            echo json_encode(["message" => "Usuario creado", "id" => $id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear usuario"]);
        }
    }

    // PUT /usuarios/{id}
    public function update($id, $input) {
        $model = new Usuario($this->conn);
        $model->id = $id;
        $model->nickname = $input['nickname'] ?? null;
        $model->nombre = $input['nombre'] ?? null;
        $model->apellido = $input['apellido'] ?? null;
        $model->email = $input['email'] ?? null;
        if (isset($input['password'])) {
            $model->password = password_hash($input['password'], PASSWORD_DEFAULT);
        }
        $model->fecha_nacimiento = $input['fecha_nacimiento'] ?? null;
        $model->puntuacion = $input['puntuacion'] ?? 0;

        if ($model->update()) {
            echo json_encode(["message" => "Usuario actualizado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // DELETE /usuarios/{id}
    public function delete($id) {
        $model = new Usuario($this->conn);
        if ($model->delete($id)) {
            echo json_encode(["message" => "Usuario eliminado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
