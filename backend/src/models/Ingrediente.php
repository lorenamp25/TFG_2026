<?php

// Clase modelo que representa la tabla "ingredientes"
class Ingrediente {
    // Conexión a la base de datos
    private $conn;
    // Nombre de la tabla asociada
    private $table_name = "ingredientes";

    // Propiedades públicas que coinciden con las columnas de la tabla
    public $id;
    public $nombre;

    // Constructor: recibe la conexión PDO y la guarda en $conn
    public function __construct($db) {
        $this->conn = $db;
    }

    // ---------------------------------------------------------
    // Obtener todos los ingredientes
    // ---------------------------------------------------------
    public function getAll() {
        // Consulta SQL para obtener todos los ingredientes ordenados del más nuevo al más viejo
        $query = "SELECT id, nombre FROM " . $this->table_name . " ORDER BY nombre DESC";

        // Prepara la consulta para evitar inyecciones SQL
        $stmt = $this->conn->prepare($query);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve todas las filas como un array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------
    // Obtener un ingrediente según su ID
    // ---------------------------------------------------------
    public function getById($id) {
        // Consulta SQL con parámetro para buscar un ingrediente específico
        $query = "SELECT id, nombre FROM " . $this->table_name . " WHERE id = :id LIMIT 1";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Vincula el valor del ID recibido al parámetro SQL
        $stmt->bindParam(":id", $id);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve una sola fila (o false si no existe)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------
    // Crear un nuevo ingrediente
    // ---------------------------------------------------------
    public function create() {
        // Consulta SQL de inserción con parámetro nombrado
        $query = "INSERT INTO " . $this->table_name . " (nombre) VALUES (:nombre)";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia el nombre para evitar HTML e inyecciones
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));

        // Vincula el nombre al parámetro de la consulta
        $stmt->bindParam(":nombre", $this->nombre);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Devuelve el ID del nuevo ingrediente
            return $this->conn->lastInsertId();
        }

        // Si falla, devuelve false
        return false;
    }

    // ---------------------------------------------------------
    // Actualizar un ingrediente existente
    // ---------------------------------------------------------
    public function update() {
        // Consulta SQL de actualización por ID
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre WHERE id = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia el nombre e ID
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Enlaza los parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":id", $this->id);

        // Ejecuta la actualización
        return $stmt->execute();
    }

    // ---------------------------------------------------------
    // Eliminar un ingrediente por su ID
    // ---------------------------------------------------------
    public function delete($id) {
        // Consulta SQL para borrar un ingrediente
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Enlaza el ID al parámetro
        $stmt->bindParam(":id", $id);

        // Ejecuta la eliminación y devuelve true/false
        return $stmt->execute();
    }

}
