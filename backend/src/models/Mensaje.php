<?php

class Mensaje {
    private $conn;
    private $table_name = "mensajes";

    public $id;
    public $remitente;
    public $destinatario;
    public $asunto;
    public $contenido;
    public $fecha_envio;
    public $leido;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los mensajes
    public function getAll() {
        $query = "SELECT id, remitente, destinatario, asunto, contenido, fecha_envio, leido FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener por id
    public function getById($id) {
        $query = "SELECT id, remitente, destinatario, asunto, contenido, fecha_envio, leido FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (remitente, destinatario, asunto, contenido, fecha_envio, leido) VALUES (:remitente, :destinatario, :asunto, :contenido, :fecha_envio, :leido)";
        $stmt = $this->conn->prepare($query);

        $this->remitente = htmlspecialchars(strip_tags($this->remitente));
        $this->destinatario = htmlspecialchars(strip_tags($this->destinatario));
        $this->asunto = $this->asunto !== null ? htmlspecialchars(strip_tags($this->asunto)) : null;
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->fecha_envio = $this->fecha_envio ?? date('Y-m-d H:i:s');
        $this->leido = $this->leido ?? 0;

        $stmt->bindParam(":remitente", $this->remitente);
        $stmt->bindParam(":destinatario", $this->destinatario);
        $stmt->bindParam(":asunto", $this->asunto);
        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":fecha_envio", $this->fecha_envio);
        $stmt->bindParam(":leido", $this->leido);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Actualizar
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET contenido = :contenido, leido = :leido WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->leido = $this->leido ?? 0;
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":leido", $this->leido);
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