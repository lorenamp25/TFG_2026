<?php

// Clase modelo que representa la tabla "usuarios"
class Usuario {
    // Conexión a la base de datos (objeto PDO)
    private $conn;
    // Nombre de la tabla en la BD
    private $table_name = "usuarios";

    // Campos que corresponden a las columnas de la tabla
    public $id;
    public $nickname;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $fecha_nacimiento;
    public $puntuacion;
    public $es_admin;

    // Constructor: recibe la conexión a la BD y la guarda
    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los usuarios
    public function getAll() {
        // Consulta SQL para traer todos los usuarios ordenados del más reciente al más antiguo
        $query = "SELECT id, nickname, nombre, apellido, email, fecha_nacimiento, puntuacion, es_admin FROM " . $this->table_name . " ORDER BY id DESC";
        // Preparar la consulta
        $stmt = $this->conn->prepare($query);
        // Ejecutar
        $stmt->execute();
        // Devolver todos los resultados como array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por id
    public function getById($id) {
        // Consulta SQL para traer un usuario por su ID
        $query = "SELECT id, nickname, nombre, apellido, email, fecha_nacimiento, puntuacion, es_admin FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        // Asociar el parámetro :id con la variable $id
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        // Retornar el resultado o false si no existe
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por email
    public function getByMail($email) {
        // Consulta SQL para buscar un usuario por su email
        $query = "SELECT id, nickname, nombre, apellido, email, fecha_nacimiento, puntuacion, es_admin FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        // Bind del parámetro email
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        // Devolver el registro encontrado (o false)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear usuario
    public function create() {
        // Consulta INSERT con RETURNING id (propio de PostgreSQL)
        $query = "INSERT INTO " . $this->table_name . " (nickname, nombre, apellido, email, password, fecha_nacimiento, puntuacion, es_admin) VALUES (:nickname, :nombre, :apellido, :email, :password, :fecha_nacimiento, :puntuacion, :es_admin) RETURNING id";
        $stmt = $this->conn->prepare($query);

        // Garantizar que los valores no nulos tengan un valor por defecto
        $this->nickname = $this->nickname ?? null;
        $this->nombre = $this->nombre ?? null;
        $this->apellido = $this->apellido ?? null;
        $this->email = $this->email ?? null;
        $this->password = $this->password ?? null; // Se asume que ya viene hasheada
        $this->fecha_nacimiento = $this->fecha_nacimiento ?? null;
        $this->puntuacion = $this->puntuacion ?? 0;
        $this->es_admin = $this->es_admin ?? false;

        // Ejecutar el INSERT con los parámetros asignados
        $stmt->execute([
            ':nickname' => $this->nickname,
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':password' => $this->password,
            ':fecha_nacimiento' => $this->fecha_nacimiento,
            ':puntuacion' => $this->puntuacion,
            ':es_admin' => $this->es_admin
        ]);

        // Devolver el id del usuario recién creado
        return $stmt->fetchColumn();
    }

    // Actualizar usuario
    public function update() {
        // Consulta UPDATE para actualizar todos los campos a partir del id
        $query = "UPDATE " . $this->table_name . " SET nickname = :nickname, nombre = :nombre, apellido = :apellido, email = :email, password = :password, fecha_nacimiento = :fecha_nacimiento, puntuacion = :puntuacion, es_admin = :es_admin WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitizar el ID
        $this->id = htmlspecialchars(strip_tags($this->id));
        // Valores con fallback en caso de null
        $this->nickname = $this->nickname ?? null;
        $this->nombre = $this->nombre ?? null;
        $this->apellido = $this->apellido ?? null;
        $this->email = $this->email ?? null;
        $this->password = $this->password ?? null;
        $this->fecha_nacimiento = $this->fecha_nacimiento ?? null;
        $this->puntuacion = $this->puntuacion ?? 0;
        $this->es_admin = $this->es_admin ?? false;

        // Ejecutar la actualización
        return $stmt->execute([
            ':nickname' => $this->nickname,
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':password' => $this->password,
            ':fecha_nacimiento' => $this->fecha_nacimiento,
            ':puntuacion' => $this->puntuacion,
            ':es_admin' => $this->es_admin,
            ':id' => $this->id
        ]);
    }

    // Eliminar usuario
    public function delete($id) {
        // Consulta DELETE para borrar un usuario por su ID
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        // Ejecutar pasando el id como parámetro
        return $stmt->execute([':id' => $id]);
    }
}
