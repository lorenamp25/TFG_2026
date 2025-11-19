<?php
// Importa el modelo Comentario, que contiene la lógica para interactuar con la BD
require_once __DIR__ . '/../models/Comentario.php';

// Define la clase del controlador para manejar comentarios
class ComentarioController {

    // Propiedad para guardar la conexión a la base de datos
    private $conn;

    // Constructor: recibe la conexión $db y la almacena
    public function __construct($db) {
        $this->conn = $db;
    }

    // ---------------------------------------------------------------
    // GET /comentarios → Obtiene todos los comentarios
    // ---------------------------------------------------------------
    public function index() {
        // Crea una instancia del modelo Comentario
        $model = new Comentario($this->conn);

        // Llama a getAll(), que devuelve todos los registros
        $data = $model->getAll();

        // Devuelve la lista en formato JSON
        echo json_encode($data);
    }

    // ---------------------------------------------------------------
    // GET /comentarios/{id} → Obtiene un comentario por su ID
    // ---------------------------------------------------------------
    public function show($id) {
        // Instancia del modelo
        $model = new Comentario($this->conn);

        // Busca un comentario concreto llamando a getById()
        $item = $model->getById($id);

        // Si existe, lo muestra como JSON
        if ($item) {
            echo json_encode($item);

        // Si no existe, responde con error 404
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Comentario no encontrado"]);
        }
    }

    // ---------------------------------------------------------------
    // OPTIONS /comentarios → Respuesta para preflight de CORS
    // ---------------------------------------------------------------
    public function options() {
        // Permite cualquier origen
        header('Access-Control-Allow-Origin: *');

        // Permite los métodos HTTP especificados
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Permite cabeceras específicas
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Devuelve 204 (sin contenido)
        http_response_code(204);

        // Termina la ejecución de la petición OPTIONS aquí
        exit;
    }

    // ---------------------------------------------------------------
    // POST /comentarios → Crear un nuevo comentario
    // ---------------------------------------------------------------
    public function create($input) {

        // Validación muy básica: revisa si faltan campos obligatorios
        if (!isset($input['receta_id']) || !isset($input['usuario_id']) || !isset($input['contenido'])) {
            http_response_code(400); // Petición incorrecta
            echo json_encode(["error" => "Campos obligatorios: receta_id, usuario_id, contenido"]);
            return; // Finaliza la ejecución
        }

        // Instancia el modelo Comentario
        $model = new Comentario($this->conn);

        // Asigna los valores recibidos al modelo
        $model->receta_id = $input['receta_id'];
        $model->usuario_id = $input['usuario_id'];
        $model->contenido = $input['contenido'];

        // La puntuación puede ser null si no se envía
        $model->puntuacion = $input['puntuacion'] ?? null;

        // Llama al método create() y obtiene el ID insertado
        $id = $model->create();

        // Si se insertó correctamente
        if ($id) {
            http_response_code(201); // Recurso creado
            echo json_encode(["message" => "Comentario creado", "id" => $id]);
        } else {
            // Si hubo fallo al insertar
            http_response_code(500); // Error interno
            echo json_encode(["error" => "Error al crear el comentario"]);
        }
    }

    // ---------------------------------------------------------------
    // PUT /comentarios/{id} → Actualizar un comentario ya existente
    // ---------------------------------------------------------------
    public function update($id, $input) {

        // Instancia el modelo Comentario
        $model = new Comentario($this->conn);

        // Asigna el ID del comentario a actualizar
        $model->id = $id;

        // Asigna los campos recibidos (si no existen, quedan en null)
        $model->contenido = $input['contenido'] ?? null;
        $model->puntuacion = $input['puntuacion'] ?? null;

        // Validación: el contenido no puede estar vacío
        if (!$model->contenido) {
            http_response_code(400); // Petición incorrecta
            echo json_encode(["error" => "El campo 'contenido' es obligatorio"]);
            return;
        }

        // Si la BD actualizó el registro con éxito
        if ($model->update()) {
            echo json_encode(["message" => "Comentario actualizado"]);

        // Si falló o no existe el comentario
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // ---------------------------------------------------------------
    // DELETE /comentarios/{id} → Eliminar un comentario
    // ---------------------------------------------------------------
    public function delete($id) {
        // Instancia el modelo
        $model = new Comentario($this->conn);

        // Si la eliminación fue exitosa
        if ($model->delete($id)) {
            echo json_encode(["message" => "Comentario eliminado"]);

        // Si falló o no existe
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
