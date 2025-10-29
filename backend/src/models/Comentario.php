<?php

class Comentario {
    private $conn;
    private $table_name = "comentarios";

    public $id;
    public $receta_id;
    public $usuario_id;
    public $contenido;
    public $puntuacion;
    public $fecha_creacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los comentarios
    public function getAll() {
        $query = "SELECT id, receta_id, usuario_id, contenido, puntuacion, fecha_creacion FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener por id
    public function getById($id) {
        $query = "SELECT id, receta_id, usuario_id, contenido, puntuacion, fecha_creacion FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (receta_id, usuario_id, contenido, puntuacion, fecha_creacion) VALUES (:receta_id, :usuario_id, :contenido, :puntuacion, :fecha_creacion)";
        $stmt = $this->conn->prepare($query);

        $this->receta_id = htmlspecialchars(strip_tags($this->receta_id));
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->puntuacion = $this->puntuacion !== null ? htmlspecialchars(strip_tags($this->puntuacion)) : null;
        $this->fecha_creacion = $this->fecha_creacion ?? date('Y-m-d H:i:s');

        $stmt->bindParam(":receta_id", $this->receta_id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":puntuacion", $this->puntuacion);
        $stmt->bindParam(":fecha_creacion", $this->fecha_creacion);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Actualizar
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET contenido = :contenido, puntuacion = :puntuacion WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->puntuacion = $this->puntuacion !== null ? htmlspecialchars(strip_tags($this->puntuacion)) : null;
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":puntuacion", $this->puntuacion);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Eliminar
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

}