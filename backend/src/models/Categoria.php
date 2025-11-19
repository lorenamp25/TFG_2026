<?php

// Clase que representa el modelo Categoria (acceso a la tabla 'categorias')
class Categoria {
    // Conexión a la base de datos (objeto PDO)
    private $conn;
    // Nombre de la tabla en la base de datos
    private $table_name = "categorias";

    // Propiedades públicas que representan las columnas de la tabla
    public $id;
    public $nombre;
    public $descripcion;
    public $icono;

    // El constructor recibe la conexión a la base de datos y la guarda en $conn
    public function __construct($db) {
        $this->conn = $db;
    }

    // -------------------------------------------------------
    // Obtener todas las categorías de la tabla
    // -------------------------------------------------------
    public function getAll() {
        // Consulta SQL: selecciona todos los campos que interesan, ordenados por id descendente
        $query = "SELECT id, nombre, descripcion, icono FROM " . $this->table_name . " ORDER BY id DESC";
        // Prepara la consulta en PDO
        $stmt = $this->conn->prepare($query);
        // Ejecuta la consulta
        $stmt->execute();
        // Devuelve todos los resultados como array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // Obtener una categoría según su ID
    // -------------------------------------------------------
    public function getById($id) {
        // Consulta SQL con parámetro :id para obtener solo una categoría
        $query = "SELECT id, nombre, descripcion, icono FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        // Prepara la consulta
        $stmt = $this->conn->prepare($query);
        // Vincula el valor del ID al parámetro :id
        $stmt->bindParam(":id", $id);
        // Ejecuta la consulta
        $stmt->execute();
        // Devuelve solo una fila (o false si no hay resultados)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // Crear una nueva categoría en la base de datos
    // -------------------------------------------------------
    public function create() {
        // Consulta de inserción con parámetros nombrados
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion, icono) VALUES (:nombre, :descripcion, :icono)";
        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia el nombre para evitar caracteres raros / HTML
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        // Vincula cada campo al parámetro correspondiente
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":icono", $this->icono);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Si todo va bien, devuelve el último ID insertado
            return $this->conn->lastInsertId();
        }

        // Si falla la ejecución, devuelve false
        return false;
    }

    // -------------------------------------------------------
    // Actualizar una categoría existente
    // -------------------------------------------------------
    public function update() {
        // Consulta SQL de actualización con parámetros
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, descripcion = :descripcion, icono = :icono  WHERE id = :id";
        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia el nombre e id para evitar caracteres peligrosos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincula los valores a los parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":icono", $this->icono);
        $stmt->bindParam(":id", $this->id);

        // Devuelve true si la actualización fue correcta, false si falló
        return $stmt->execute();
    }

    // -------------------------------------------------------
    // Eliminar una categoría por su ID
    // -------------------------------------------------------
    public function delete($id) {
        // Consulta SQL para eliminar por ID
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        // Prepara la consulta
        $stmt = $this->conn->prepare($query);
        // Vincula el ID al parámetro :id
        $stmt->bindParam(":id", $id);
        // Ejecuta y devuelve true/false según el resultado
        return $stmt->execute();
    }
}
