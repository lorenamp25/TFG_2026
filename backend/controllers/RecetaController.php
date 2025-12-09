<?php
// Importa el modelo Receta, encargado de manejar todas las operaciones con la base de datos
require_once __DIR__ . '/../models/Receta.php';

// Define la clase controladora que gestionará las recetas
class RecetaController
{

    // Propiedad donde se almacena la conexión a la base de datos
    private $conn;

    // Constructor: recibe la conexión $db y la asigna a $this->conn
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ----------------------------------------------------------
    // OPTIONS /recetas → Respuesta a peticiones CORS (preflight)
    // ----------------------------------------------------------
    public function options()
    {
        // Permite cualquier origen para las solicitudes
        header('Access-Control-Allow-Origin: *');

        // Permite estos métodos HTTP
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // Permite estos headers
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Respuesta vacía con estado 204 (sin contenido)
        http_response_code(204);

        // Termina la ejecución para solicitudes OPTIONS
        exit;
    }

    // ----------------------------------------------------------
    // GET /recetas → Devuelve todas las recetas de la base de datos
    // ----------------------------------------------------------
    public function index()
    {
        // Crea una instancia del modelo Receta
        $model = new Receta($this->conn);

        // Obtiene todas las recetas llamando a getAll()
        $data = $model->getAll();

        // Devuelve la lista como JSON
        echo json_encode($data);
    }

    // ----------------------------------------------------------
    // GET /recetas/{id} → Devuelve una receta por ID
    // ----------------------------------------------------------
    public function show($id)
    {
        // Instancia el modelo Receta
        $model = new Receta($this->conn);

        // Busca una receta según su ID
        $receta = $model->getById($id);

        // Si existe, se devuelve como JSON
        if ($receta) {
            echo json_encode($receta);

            // Si no existe el ID, devuelve error 404
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Receta no encontrada"]);
        }
    }

    // ----------------------------------------------------------
    // POST /recetas → Crear una nueva receta
    // ----------------------------------------------------------
    public function create($input)
    {
        $input = [
            'titulo' => $_POST['titulo'] ?? null,
            'descripcion' => $_POST['descripcion'] ?? null,
            'tiempoPreparacion' => $_POST['tiempo_preparacion'] ?? null,
            'dificultad' => $_POST['dificultad'] ?? null,
            'categoria' => $_POST['categoria'] ?? null,
            'imagen_url' => $_POST['imagen_url'] ?? null,
            'imagen_cambiada' => $_POST['imagen_cambiada'] ?? null,
            'usuario' => isset($_POST['usuario']) ? json_decode($_POST['usuario'], true) : null,
            'ingredientes' => isset($_POST['ingredientes']) ? json_decode($_POST['ingredientes'], true) : [],
            'instrucciones' => isset($_POST['instrucciones']) ? json_decode($_POST['instrucciones'], true) : []
        ];

        // Validación: el título es obligatorio
        if (!isset($input['titulo']) || empty(trim($input['titulo']))) {
            http_response_code(400); // Petición inválida
            echo json_encode(["error" => "El campo 'titulo' es obligatorio"]);
            return;
        }

        // Procesar imagen si existe
        if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/recetas/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . $_FILES['imagen_principal']['name'];
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $filePath)) {
                $input['imagen_url'] = $filePath;
            }
        }


        // Crea instancia del modelo Receta
        $model = new Receta($this->conn);

        // Asigna los valores recibidos al modelo
        $model->titulo = $input['titulo'];
        $model->descripcion = $input['descripcion'] ?? null;
        $model->tiempo_preparacion = $input['tiempoPreparacion'] ?? null;
        $model->dificultad = $input['dificultad'] ?? null;
        $model->categoria = $input['categoria'] ?? null;
        $model->imagen_url = $input['imagen_url'] ?? null;
        $model->usuario = $input['usuario'] ?? null;

        // Asigna listas relacionadas: ingredientes e instrucciones
        $model->ingredientes = $input['ingredientes'] ?? [];
        $model->instrucciones = $input['instrucciones'] ?? [];

        // Inserta la receta y obtiene el ID generado
        $id = $model->create();

        // Si la creación fue exitosa
        if ($id) {
            http_response_code(201); // Recurso creado
            echo json_encode(["message" => "Receta creada", "id" => $id]);

            // Si ocurrió un error al crear la receta
        } else {
            http_response_code(500); // Error interno
            $err = $model->lastError ?? null; // Mensaje de error técnico si existe
            echo json_encode(["error" => "Error al crear la receta", "detail" => $err]);
        }
    }

    // ----------------------------------------------------------
    // PUT /recetas/{id} → Actualizar una receta existente
    // ----------------------------------------------------------
    public function update($id, $input)
    {
        $input = [
            'titulo' => $_POST['titulo'] ?? null,
            'descripcion' => $_POST['descripcion'] ?? null,
            'tiempoPreparacion' => $_POST['tiempo_preparacion'] ?? null,
            'dificultad' => $_POST['dificultad'] ?? null,
            'categoria' => $_POST['categoria'] ?? null,
            'imagen_url' => $_POST['imagen_url'] ?? null,
            'imagen_cambiada' => $_POST['imagen_cambiada'] ?? null,
            'usuario' => isset($_POST['usuario']) ? json_decode($_POST['usuario'], true) : null,
            'ingredientes' => isset($_POST['ingredientes']) ? json_decode($_POST['ingredientes'], true) : [],
            'instrucciones' => isset($_POST['instrucciones']) ? json_decode($_POST['instrucciones'], true) : []
        ];

        // Validación: el título es obligatorio
        if (!isset($input['titulo']) || empty(trim($input['titulo']))) {
            http_response_code(400); // Petición inválida
            echo json_encode(["error" => "El campo 'titulo' es obligatorio"]);
            return;
        }

        // Procesar imagen si existe
        if ($input['imagen_cambiada'] === 'true' || $input['imagen_cambiada'] === true) {
            if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/recetas/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = uniqid() . '_' . $_FILES['imagen_principal']['name'];
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $filePath)) {
                    $input['imagen_url'] = $filePath;
                }
            }
        }

        // Crear instancia del modelo Receta
        $model = new Receta($this->conn);

        // Asigna el ID de la receta a actualizar
        $model->id = $id;

        // Actualiza los campos con los valores recibidos
        $model->titulo = $input['titulo'] ?? null;
        $model->descripcion = $input['descripcion'] ?? null;
        $model->tiempo_preparacion = $input['tiempo_preparacion'] ?? null;
        $model->dificultad = $input['dificultad'] ?? null;
        $model->categoria = $input['categoria'] ?? null;
        $model->imagen_url = $input['imagen_url'] ?? null;
        $model->usuario = $input['usuario_id'] ?? null;

        // Actualiza listas relacionadas
        $model->ingredientes = $input['ingredientes'] ?? [];
        $model->instrucciones = $input['instrucciones'] ?? [];

        // Validación: el título no puede ser null
        if (!$model->titulo) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'titulo' es obligatorio"]);
            return;
        }

        // Si update() devuelve true, la receta se actualizó
        if ($model->update()) {
            echo json_encode(["message" => "Receta actualizada"]);

            // Si no se pudo actualizar (id inexistente o error)
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    }

    // ----------------------------------------------------------
    // DELETE /recetas/{id} → Eliminar una receta
    // ----------------------------------------------------------
    public function delete($id)
    {
        // Instancia el modelo Receta
        $model = new Receta($this->conn);

        // Si se elimina correctamente
        if ($model->delete($id)) {
            echo json_encode(["message" => "Receta eliminada"]);

            // Si no existe la receta o falla el borrado
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
