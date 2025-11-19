<?php

// Clase que representa el modelo "Comentario" y gestiona el acceso a la tabla comentarios
class Comentario {
    // Conexión a la base de datos (PDO)
    private $conn;
    // Nombre de la tabla asociada
    private $table_name = "comentarios";

    // Propiedades públicas que representan cada columna de la tabla
    public $id;
    public $receta_id;
    public $usuario_id;
    public $contenido;
    public $puntuacion;
    public $fecha_creacion;

    // Constructor: recibe la conexión a la base de datos y la guarda
    public function __construct($db) {
        $this->conn = $db;
    }

    // --------------------------------------------------------
    // Obtener todos los comentarios almacenados
    // --------------------------------------------------------
    public function getAll() {
        // Consulta SQL para seleccionar todos los comentarios ordenados del más nuevo al más viejo
        $query = "SELECT id, receta_id, usuario_id, contenido, puntuacion, fecha_creacion FROM " . $this->table_name . " ORDER BY id DESC";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve todos los resultados como array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --------------------------------------------------------
    // Obtener un solo comentario según su ID
    // --------------------------------------------------------
    public function getById($id) {
        // Consulta SQL para obtener un comentario concreto
        $query = "SELECT id, receta_id, usuario_id, contenido, puntuacion, fecha_creacion 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Vincula el ID al parámetro SQL
        $stmt->bindParam(":id", $id);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve la fila encontrada o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --------------------------------------------------------
    // Crear un nuevo comentario
    // --------------------------------------------------------
    public function create() {
        // Consulta SQL de inserción con parámetros nombrados
        $query = "INSERT INTO " . $this->table_name . " 
                 (receta_id, usuario_id, contenido, puntuacion, fecha_creacion) 
                 VALUES (:receta_id, :usuario_id, :contenido, :puntuacion, :fecha_creacion)";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia los datos para evitar inyección de HTML o caracteres extraños
        $this->receta_id = htmlspecialchars(strip_tags($this->receta_id));
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));

        // Si la puntuación no es null, también se limpia
        $this->puntuacion = $this->puntuacion !== null 
            ? htmlspecialchars(strip_tags($this->puntuacion)) 
            : null;

        // Si no se definió fecha_creacion, se usa la actual
        $this->fecha_creacion = $this->fecha_creacion ?? date('Y-m-d H:i:s');

        // Enlace de valores a parámetros de la consulta
        $stmt->bindParam(":receta_id", $this->receta_id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":puntuacion", $this->puntuacion);
        $stmt->bindParam(":fecha_creacion", $this->fecha_creacion);

        // Ejecuta la inserción
        if ($stmt->execute()) {
            // Devuelve el ID del comentario recién insertado
            return $this->conn->lastInsertId();
        }

        // Si falló, devuelve false
        return false;
    }

    // --------------------------------------------------------
    // Actualizar un comentario existente
    // --------------------------------------------------------
    public function update() {
        // Consulta SQL de actualización por ID
        $query = "UPDATE " . $this->table_name . " 
                  SET contenido = :contenido, puntuacion = :puntuacion 
                  WHERE id = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Limpia el contenido y la puntuación
        $this->contenido = htmlspecialchars(strip_tags($this->contenido));
        $this->puntuacion = $this->puntuacion !== null 
            ? htmlspecialchars(strip_tags($this->puntuacion)) 
            : null;

        // Limpia el ID
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincula los datos
        $stmt->bindParam(":contenido", $this->contenido);
        $stmt->bindParam(":puntuacion", $this->puntuacion);
        $stmt->bindParam(":id", $this->id);

        // Ejecuta y devuelve true/false según el resultado
        return $stmt->execute();
    }

    // --------------------------------------------------------
    // Eliminar un comentario por ID
    // --------------------------------------------------------
    public function delete($id) {
        // Consulta SQL que borra un comentario específico
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Vincula el ID
        $stmt->bindParam(":id", $id);

        // Ejecuta el borrado
        return $stmt->execute();
    }

}
