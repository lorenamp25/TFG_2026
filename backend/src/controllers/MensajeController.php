<?php
// Importa el archivo del modelo Mensaje, encargado de interactuar con la base de datos
require_once __DIR__ . '/../models/Mensaje.php';

// Define la clase controladora para manejar mensajes
class MensajeController {

    // Propiedad privada donde se almacena la conexión a la base de datos
    private $conn;

    // Constructor: recibe la conexión $db y la guarda en $conn
    public function __construct($db) {
        $this->conn = $db;
    }

    // -----------------------------------------------------------
    // GET /mensajes → Devuelve todos los mensajes existentes
    // -----------------------------------------------------------
    public function index() {
        // Crea una instancia del modelo Mensaje
        $model = new Mensaje($this->conn);

        // Obtiene todos los mensajes llamando a getAll()
        $data = $model->getAll();

        // Devuelve los datos en formato JSON
        echo json_encode($data);
    }

    // -----------------------------------------------------------
    // GET /mensajes/{id} → Devuelve un mensaje por su ID
    // -----------------------------------------------------------
    public function show($id) {
        // Instancia el modelo Mensaje
        $model = new Mensaje($this->conn);

        // Busca el mensaje según el ID
        $item = $model->getById($id);

        // Si existe lo devuelve
        if ($item) {
            echo json_encode($item);

        // Si no existe, envía un error 404
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Mensaje no encontrado"]);
        }
    }

    // -----------------------------------------------------------
    // OPTIONS /mensajes → Respuesta CORS para preflight
    // -----------------------------------------------------------
    public function options() {
        // Permite solicitudes desde cualquier origen
        header('Access-Control-Allow-Origin: *');

        // Métodos HTTP permitidos
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Cabeceras permitidas
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Código 204: éxito sin contenido
        http_response_code(204);

        // Termina aquí la respuesta OPTIONS
        exit;
    }

    // -----------------------------------------------------------
    // POST /mensajes → Crear un nuevo mensaje
    // -----------------------------------------------------------
    public function create($input) {

        // Validación mínima: revisa si faltan campos obligatorios
        if (!isset($input['remitente']) || !isset($input['destinatario']) || !isset($input['contenido'])) {
            http_response_code(400); // Petición incorrecta
            echo json_encode(["error" => "Campos obligatorios: remitente, destinatario, contenido"]);
            return; // Detener ejecución
        }

        // Crea una instancia del modelo Mensaje
        $model = new Mensaje($this->conn);

        // Asigna los datos recibidos al modelo
        $model->remitente = $input['remitente'];
        $model->destinatario = $input['destinatario'];

        // 'asunto' es opcional, por eso se usa null si no viene
        $model->asunto = $input['asunto'] ?? null;

        // El contenido sí es obligatorio
        $model->contenido = $input['contenido'];

        // Llama al método create() para insertar en la BD
        $id = $model->create();

        // Si la inserción fue exitosa
        if ($id) {
            http_response_code(201); // Recurso creado
            echo json_encode(["message" => "Mensaje creado", "id" => $id]);

        // Si falló la inserción
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["error" => "Error al crear el mensaje"]);
        }
    }

    // -----------------------------------------------------------
    // PUT /mensajes/{id} → Actualizar un mensaje existente
    // -----------------------------------------------------------
    public function update($id, $input) {

        // Instancia el modelo Mensaje
        $model = new Mensaje($this->conn);

        // Asigna el ID del mensaje que se actualizará
        $model->id = $id;

        // Asigna el contenido recibido (si no viene, será null)
        $model->contenido = $input['contenido'] ?? null;

        // Validación: no se permite contenido vacío
        if (!$model->contenido) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'contenido' es obligatorio"]);
            return;
        }

        // Si actualizar() devuelve true, la actualización fue exitosa
        if ($model->update()) {
            echo json_encode(["message" => "Mensaje actualizado"]);

        // Si no existe el mensaje o falla la actualización
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // -----------------------------------------------------------
    // DELETE /mensajes/{id} → Eliminar un mensaje
    // -----------------------------------------------------------
    public function delete($id) {

        // Instancia el modelo Mensaje
        $model = new Mensaje($this->conn);

        // Si delete() devuelve true, se eliminó correctamente
        if ($model->delete($id)) {
            echo json_encode(["message" => "Mensaje eliminado"]);

        // Si no existe o ocurre error
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
