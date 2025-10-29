<?php

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nickname;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $fecha_nacimiento;
    public $puntuacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los usuarios
    public function getAll() {
        $query = "SELECT id, nickname, nombre, apellido, email, fecha_nacimiento, puntuacion FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener por id
    public function getById($id) {
        $query = "SELECT id, nickname, nombre, apellido, email, fecha_nacimiento, puntuacion FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear usuario
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nickname, nombre, apellido, email, password, fecha_nacimiento, puntuacion) VALUES (:nickname, :nombre, :apellido, :email, :password, :fecha_nacimiento, :puntuacion) RETURNING id";
        $stmt = $this->conn->prepare($query);

        $this->nickname = $this->nickname ?? null;
        $this->nombre = $this->nombre ?? null;
        $this->apellido = $this->apellido ?? null;
        $this->email = $this->email ?? null;
        $this->password = $this->password ?? null; // assume already hashed
        $this->fecha_nacimiento = $this->fecha_nacimiento ?? null;
        $this->puntuacion = $this->puntuacion ?? 0;

        $stmt->execute([
            ':nickname' => $this->nickname,
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':password' => $this->password,
            ':fecha_nacimiento' => $this->fecha_nacimiento,
            ':puntuacion' => $this->puntuacion
        ]);

        return $stmt->fetchColumn();
    }

    // Actualizar usuario
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nickname = :nickname, nombre = :nombre, apellido = :apellido, email = :email, password = :password, fecha_nacimiento = :fecha_nacimiento, puntuacion = :puntuacion WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nickname = $this->nickname ?? null;
        $this->nombre = $this->nombre ?? null;
        $this->apellido = $this->apellido ?? null;
        $this->email = $this->email ?? null;
        $this->password = $this->password ?? null;
        $this->fecha_nacimiento = $this->fecha_nacimiento ?? null;
        $this->puntuacion = $this->puntuacion ?? 0;

        return $stmt->execute([
            ':nickname' => $this->nickname,
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':password' => $this->password,
            ':fecha_nacimiento' => $this->fecha_nacimiento,
            ':puntuacion' => $this->puntuacion,
            ':id' => $this->id
        ]);
    }

    // Eliminar usuario
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }


    

}

