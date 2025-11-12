<?php

class Categoria {
    private $conn;
    private $table_name = "categorias";

    public $id;
    public $nombre;
    public $descripcion;
    public $icono;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las categorías
    public function getAll() {
        $query = "SELECT id, nombre, descripcion, icono FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una categoría por ID
    public function getById($id) {
        $query = "SELECT id, nombre, descripcion, icono FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva categoría
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion, icono) VALUES (:nombre, :descripcion, :icono)";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":icono", $this->icono);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Actualizar una categoría
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, descripcion = :descripcion, icono = :icono  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":icono", $this->icono);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Eliminar una categoría
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}