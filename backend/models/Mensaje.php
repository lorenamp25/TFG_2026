<?php

// Clase modelo que representa la tabla "mensajes"
class Mensaje {
    // Conexión a la base de datos (PDO)
    private $conn;
    // Nombre de la tabla asociada
    private $table_name = "mensajes";

    // Propiedades públicas que representan las columnas de la tabla
    public $id;
    public $remitente;
    public $destinatario;
    public $asunto;
    public $contenido;
    public $fecha_envio;
    public $leido;

    // Constructor: recibe conexión PDO y la guarda en el objeto
    public function __construct($db) {
        $this->conn = $db;
    }

    // ---------------------------------------------------------
    // Obtener todos los mensajes
    // ---------------------------------------------------------
    public function getAll() {
        // Consulta SQL que trae todos los mensajes ordenados del más nuevo al más viejo
        $query = "SELECT id, remitente, destinatario, asunto, contenido, fecha_envio, leido 
                  FROM " . $this->table_name . " 
                  ORDER BY id DESC";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve todos los registros como array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------
    // Obtener un mensaje por su ID
    // ---------------------------------------------------------
    public function getById($id) {
        // Consulta SQL para traer solo un mensaje específico
        $query = "SELECT id, remitente, destinatario, asunto, contenido, fecha_envio, leido 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Vincula el parámetro :id al valor real
        $stmt->bindParam(":id", $id);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve una sola fila o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------
    // Crear un mensaje nuevo
    // ---------------------------------------------------------
    public function create() {
        // Consulta SQL de inserción con parámetros nombrados
        $query = "INSERT INTO " . $this->table_name . " 
                 (remitente, destinatario, asunto, contenido, fecha_envio, leido) 
                 VALUES (:remitente, :destinatario, :asunto, :contenido, :fecha_envio, :leido)";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia los valores (quita etiquetas HTML o caracteres peligrosos)
        $this->remitente = htmlspecialchars(strip_tags($this->remitente));
        $this->destinatario = htmlspecialchars(strip_tags($this->destinatario));

        // Si hay asunto, lo limpia; si no, queda como null
        $this->asunto = $this->asunto !== null 
            ? htmlspecialchars(strip_tags($this->asunto)) 
            : null;

        // Limpia el contenido del mensaje
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));

        // Si no se pasó fecha, se usa la fecha actual del sistema
        $this->fecha_envio = $this->fecha_envio ?? date('Y-m-d H:i:s');

        // Si no se especifica si está leído, se pone a 0 (no leído)
        $this->leido = $this->leido ?? 0;

        // Vincula los valores a los parámetros SQL
        $stmt->bindParam(":remitente", $this->remitente);
        $stmt->bindParam(":destinatario", $this->destinatario);
        $stmt->bindParam(":asunto", $this->asunto);
        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":fecha_envio", $this->fecha_envio);
        $stmt->bindParam(":leido", $this->leido);

        // Ejecuta la inserción
        if ($stmt->execute()) {
            // Devuelve el ID del nuevo mensaje creado
            return $this->conn->lastInsertId();
        }

        // Si falla la inserción
        return false;
    }

    // ---------------------------------------------------------
    // Actualizar un mensaje (solo contenido y si está leído)
    // ---------------------------------------------------------
    public function update() {
        // Consulta SQL para actualizar contenido y estado de lectura
        $query = "UPDATE " . $this->table_name . " 
                  SET contenido = :contenido, leido = :leido 
                  WHERE id = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia el contenido
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));

        // Si no se envió leido, se mantiene como 0
        $this->leido = $this->leido ?? 0;

        // Limpia el ID
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Enlaza los parámetros
        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":leido", $this->leido);
        $stmt->bindParam(":id", $this->id);

        // Ejecuta la actualización y devuelve true/false
        return $stmt->execute();
    }

    // ---------------------------------------------------------
    // Eliminar un mensaje por ID
    // ---------------------------------------------------------
    public function delete($id) {
        // Consulta SQL para eliminar un mensaje específico
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Vincula el ID
        $stmt->bindParam(":id", $id);

        // Ejecuta y devuelve true/false
        return $stmt->execute();
    }

}
