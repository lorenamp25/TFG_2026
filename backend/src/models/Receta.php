<?php

class Receta {
    private $conn;
    private $table_name = "recetas";

    public $id;
    public $titulo;
    public $descripcion;
    // Arrays of related objects
    // ingredientes: array of { ingrediente_id, cantidad, unidad }
    // instrucciones: array of { orden, descripcion, imagen_url }
    public $ingredientes = [];
    public $instrucciones = [];
    public $lastError = null;
    public $tiempo_preparacion;
    public $dificultad;
    public $categoria;
    public $imagen_url;
    public $usuario_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las recetas (incluye ingredientes e instrucciones)
    public function getAll() {
        $query = "SELECT id, titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $row['ingredientes'] = $this->getIngredientesByRecetaId($row['id']);
            $row['instrucciones'] = $this->getInstruccionesByRecetaId($row['id']);
            $results[] = $row;
        }
        return $results;
    }

    // Obtener una receta por ID con sus relaciones
    public function getById($id) {
        $query = "SELECT id, titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $row['ingredientes'] = $this->getIngredientesByRecetaId($id);
        $row['instrucciones'] = $this->getInstruccionesByRecetaId($id);
        return $row;
    }

    // Crear receta con relaciones (usa transacción)
    public function create() {
        try {
            $this->conn->beginTransaction();

            // Use RETURNING id so we can fetch the new id in PostgreSQL
            $query = "INSERT INTO " . $this->table_name . " (titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id) VALUES (:titulo, :descripcion, :tiempo_preparacion, :dificultad, :categoria, :imagen_url, :usuario_id) RETURNING id";
            $stmt = $this->conn->prepare($query);

            $this->titulo = htmlspecialchars(strip_tags($this->titulo));
            $this->descripcion = $this->descripcion ?? null;
            $this->tiempo_preparacion = $this->tiempo_preparacion ?? null;
            $this->dificultad = $this->dificultad ?? null;
            $this->categoria = $this->categoria ?? null;
            $this->imagen_url = $this->imagen_url ?? null;
            $this->usuario_id = $this->usuario_id ?? null;

            $stmt->bindParam(":titulo", $this->titulo);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":tiempo_preparacion", $this->tiempo_preparacion);
            $stmt->bindParam(":dificultad", $this->dificultad);
            $stmt->bindParam(":categoria", $this->categoria);
            $stmt->bindParam(":imagen_url", $this->imagen_url);
            $stmt->bindParam(":usuario_id", $this->usuario_id);

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }

            // Fetch the returned id (Postgres RETURNING)
            $newId = $stmt->fetchColumn();

            // insertar ingredientes si los hay
            if (!empty($this->ingredientes) && is_array($this->ingredientes)) {
                $insQuery = "INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (:receta_id, :ingrediente_id, :cantidad, :unidad)";
                $insStmt = $this->conn->prepare($insQuery);
                foreach ($this->ingredientes as $ing) {
                    $ingrediente_id = $ing['ingrediente_id'] ?? $ing['id'] ?? null;
                    $cantidad = $ing['cantidad'] ?? null;
                    $unidad = $ing['unidad'] ?? null;
                    $insStmt->execute([
                        ':receta_id' => $newId,
                        ':ingrediente_id' => $ingrediente_id,
                        ':cantidad' => $cantidad,
                        ':unidad' => $unidad
                    ]);
                }
            }

            // insertar instrucciones si las hay
            if (!empty($this->instrucciones) && is_array($this->instrucciones)) {
                $insQuery = "INSERT INTO receta_instrucciones (receta_id, orden, descripcion, imagen_url) VALUES (:receta_id, :orden, :descripcion, :imagen_url)";
                $insStmt = $this->conn->prepare($insQuery);
                foreach ($this->instrucciones as $instr) {
                    $orden = $instr['orden'] ?? null;
                    $descripcion = $instr['descripcion'] ?? null;
                    $imagen = $instr['imagen_url'] ?? ($instr['imagen'] ?? null);
                    $insStmt->execute([
                        ':receta_id' => $newId,
                        ':orden' => $orden,
                        ':descripcion' => $descripcion,
                        ':imagen_url' => $imagen
                    ]);
                }
            }

            $this->conn->commit();
            return $newId;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // Actualizar receta y reemplazar relaciones (transacción)
    public function update() {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table_name . " SET titulo = :titulo, descripcion = :descripcion, tiempo_preparacion = :tiempo_preparacion, dificultad = :dificultad, categoria = :categoria, imagen_url = :imagen_url, usuario_id = :usuario_id WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->titulo = htmlspecialchars(strip_tags($this->titulo));
            $this->descripcion = $this->descripcion ?? null;
            $this->tiempo_preparacion = $this->tiempo_preparacion ?? null;
            $this->dificultad = $this->dificultad ?? null;
            $this->categoria = $this->categoria ?? null;
            $this->imagen_url = $this->imagen_url ?? null;
            $this->usuario_id = $this->usuario_id ?? null;
            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bindParam(":titulo", $this->titulo);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":tiempo_preparacion", $this->tiempo_preparacion);
            $stmt->bindParam(":dificultad", $this->dificultad);
            $stmt->bindParam(":categoria", $this->categoria);
            $stmt->bindParam(":imagen_url", $this->imagen_url);
            $stmt->bindParam(":usuario_id", $this->usuario_id);
            $stmt->bindParam(":id", $this->id);

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }

            // Reemplazar ingredientes: borrar existentes y volver a insertar
            $delIng = $this->conn->prepare("DELETE FROM receta_ingredientes WHERE receta_id = :receta_id");
            $delIng->execute([':receta_id' => $this->id]);

            if (!empty($this->ingredientes) && is_array($this->ingredientes)) {
                $insQuery = "INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (:receta_id, :ingrediente_id, :cantidad, :unidad)";
                $insStmt = $this->conn->prepare($insQuery);
                foreach ($this->ingredientes as $ing) {
                    $ingrediente_id = $ing['ingrediente_id'] ?? $ing['id'] ?? null;
                    $cantidad = $ing['cantidad'] ?? null;
                    $unidad = $ing['unidad'] ?? null;
                    $insStmt->execute([
                        ':receta_id' => $this->id,
                        ':ingrediente_id' => $ingrediente_id,
                        ':cantidad' => $cantidad,
                        ':unidad' => $unidad
                    ]);
                }
            }

            // Reemplazar instrucciones: borrar existentes y volver a insertar
            $delInstr = $this->conn->prepare("DELETE FROM receta_instrucciones WHERE receta_id = :receta_id");
            $delInstr->execute([':receta_id' => $this->id]);

            if (!empty($this->instrucciones) && is_array($this->instrucciones)) {
                $insQuery = "INSERT INTO receta_instrucciones (receta_id, orden, descripcion, imagen_url) VALUES (:receta_id, :orden, :descripcion, :imagen_url)";
                $insStmt = $this->conn->prepare($insQuery);
                foreach ($this->instrucciones as $instr) {
                    $orden = $instr['orden'] ?? null;
                    $descripcion = $instr['descripcion'] ?? null;
                    $imagen = $instr['imagen_url'] ?? ($instr['imagen'] ?? null);
                    $insStmt->execute([
                        ':receta_id' => $this->id,
                        ':orden' => $orden,
                        ':descripcion' => $descripcion,
                        ':imagen_url' => $imagen
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // Eliminar receta (las relaciones tienen ON DELETE CASCADE)
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Helpers para traer relaciones
    private function getIngredientesByRecetaId($recetaId) {
        $q = "SELECT ingrediente_id, cantidad, unidad FROM receta_ingredientes WHERE receta_id = :receta_id ORDER BY ingrediente_id";
        $s = $this->conn->prepare($q);
        $s->bindParam(':receta_id', $recetaId);
        $s->execute();
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getInstruccionesByRecetaId($recetaId) {
        $q = "SELECT orden, descripcion, imagen_url FROM receta_instrucciones WHERE receta_id = :receta_id ORDER BY orden";
        $s = $this->conn->prepare($q);
        $s->bindParam(':receta_id', $recetaId);
        $s->execute();
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

}