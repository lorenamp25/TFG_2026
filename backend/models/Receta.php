<?php

require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Ingrediente.php';

// Modelo que representa la tabla "recetas" y sus relaciones (ingredientes e instrucciones)
class Receta
{
    // Conexión a la base de datos (PDO)
    private $conn;
    // Nombre de la tabla principal
    private $table_name = "recetas";

    // Propiedades que representan columnas de la tabla "recetas"
    public $id;
    public $titulo;
    public $descripcion;
    // Arrays de objetos relacionados:
    // ingredientes: array de { ingrediente_id, cantidad, unidad }
    // instrucciones: array de { orden, descripcion, imagen_url }
    public $ingredientes = [];
    public $instrucciones = [];
    // Para guardar el último error (por ejemplo en excepciones)
    public $lastError = null;
    public $tiempo_preparacion;
    public $dificultad;
    public $categoria;
    public $imagen_url;
    public $usuario;
    public $destacada;
    public $votos_positivos = 0;
    public $votos_negativos = 0;

    // Constructor: recibe la conexión y la guarda
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ------------------------------------------------------------
    // Obtener todas las recetas, con sus ingredientes e instrucciones
    // ------------------------------------------------------------
    public function getAll()
    {
        // Consulta que obtiene todas las recetas con sus campos principales
        $query = "SELECT id, titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id, destacada, votos_positivos, votos_negativos FROM " . $this->table_name . " ORDER BY id DESC";
        // Prepara la consulta
        $stmt = $this->conn->prepare($query);
        // Ejecuta la consulta
        $stmt->execute();
        // Obtiene todas las filas como array asociativo
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Aquí almacenaremos las recetas con sus relaciones
        $results = [];
        // Recorre cada receta
        foreach ($rows as $row) {
            // Añade los ingredientes de esa receta
            $row['ingredientes'] = $this->getIngredientesByRecetaId($row['id']);
            // Añade las instrucciones de esa receta
            $row['instrucciones'] = $this->getInstruccionesByRecetaId($row['id']);

            $row['usuario'] =  $this->getUsuarioById($row['usuario_id']);

            // Mete la receta completa en el array de resultados
            $results[] = $row;
        }
        // Devuelve la lista de recetas completas
        return $results;
    }

    // ------------------------------------------------------------
    // Obtener una receta por ID, incluyendo ingredientes e instrucciones
    // ------------------------------------------------------------
    public function getById($id)
    {
        // Consulta para obtener una receta concreta por su id
        $query = "SELECT id, titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id, destacada, votos_positivos, votos_negativos FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        // Prepara la consulta
        $stmt = $this->conn->prepare($query);
        // Vincula el parámetro :id
        $stmt->bindParam(":id", $id);
        // Ejecuta
        $stmt->execute();
        // Obtiene la receta
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Si no encuentra receta, devuelve false
        if (!$row) return false;

        // Añade los ingredientes relacionados
        $row['ingredientes'] = $this->getIngredientesByRecetaId($id);
        // Añade las instrucciones relacionadas
        $row['instrucciones'] = $this->getInstruccionesByRecetaId($id);

        $row['usuario'] =  $this->getUsuarioById($row['usuario_id']);

        // Devuelve la receta completa
        return $row;
    }

    // ------------------------------------------------------------
    // Crear una receta nueva con ingredientes e instrucciones (transacción)
    // ------------------------------------------------------------
    public function create()
    {
        try {
            // Comienza una transacción: todo o nada
            $this->conn->beginTransaction();

            // Consulta de inserción principal en la tabla recetas con RETURNING id (PostgreSQL)
            $query = "INSERT INTO " . $this->table_name . " (titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id, destacada, votos_positivos, votos_negativos) VALUES (:titulo, :descripcion, :tiempo_preparacion, :dificultad, :categoria, :imagen_url, :usuario_id, :destacada, :votos_positivos, :votos_negativos) RETURNING id";
            // Prepara la consulta
            $stmt = $this->conn->prepare($query);

            // Limpia y normaliza los datos
            $this->titulo = htmlspecialchars(strip_tags($this->titulo));
            $this->descripcion = $this->descripcion ?? null;
            $this->tiempo_preparacion = $this->tiempo_preparacion ?? null;
            $this->dificultad = $this->dificultad ?? null;
            $this->categoria = $this->categoria ?? null;
            $this->imagen_url = $this->imagen_url ?? null;
            $this->usuario = $this->usuario ?? null;
            $this->destacada = $this->destacada ?? 'false';
            // Asegura que los votos sean enteros
            $this->votos_positivos = isset($this->votos_positivos) ? (int)$this->votos_positivos : 0;
            $this->votos_negativos = isset($this->votos_negativos) ? (int)$this->votos_negativos : 0;

            // Vincula los parámetros de la receta
            $stmt->bindParam(":titulo", $this->titulo);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":tiempo_preparacion", $this->tiempo_preparacion);
            $stmt->bindParam(":dificultad", $this->dificultad);
            $stmt->bindParam(":categoria", $this->categoria);
            $stmt->bindParam(":imagen_url", $this->imagen_url);
            $stmt->bindParam(":usuario_id", $this->usuario['id']);
            $stmt->bindParam(":destacada", $this->destacada);
            $stmt->bindParam(":votos_positivos", $this->votos_positivos);
            $stmt->bindParam(":votos_negativos", $this->votos_negativos);

            // Ejecuta la inserción de la receta
            if (!$stmt->execute()) {
                // Si falla, deshace la transacción
                $this->conn->rollBack();
                return false;
            }

            // Obtiene el ID generado por PostgreSQL (RETURNING id)
            $newId = $stmt->fetchColumn();

            // -------------------------------
            // Insertar ingredientes relacionados
            // -------------------------------
            if (!empty($this->ingredientes) && is_array($this->ingredientes)) {
                // Consulta para insertar en tabla receta_ingredientes
                $insQuery = "INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (:receta_id, :ingrediente_id, :cantidad, :unidad)";
                $insStmt = $this->conn->prepare($insQuery);
                // Recorre cada ingrediente del array
                foreach ($this->ingredientes as $ing) {
                    // Obtiene el id del ingrediente y otros campos
                    $ingrediente_id = $ing['ingrediente']['id'];
                    $cantidad = $ing['cantidad'] ?? null;
                    $unidad = $ing['unidad'] ?? null;
                    // Ejecuta inserción para esa relación
                    $insStmt->execute([
                        ':receta_id' => $newId,
                        ':ingrediente_id' => $ingrediente_id,
                        ':cantidad' => $cantidad,
                        ':unidad' => $unidad
                    ]);
                }
            }

            // -------------------------------
            // Insertar instrucciones relacionadas
            // -------------------------------
            if (!empty($this->instrucciones) && is_array($this->instrucciones)) {
                // Consulta para tabla receta_instrucciones
                $insQuery = "INSERT INTO receta_instrucciones (receta_id, orden, descripcion, imagen_url) VALUES (:receta_id, :orden, :descripcion, :imagen_url)";
                $insStmt = $this->conn->prepare($insQuery);
                foreach ($this->instrucciones as $instr) {
                    $orden = $instr['orden'] ?? null;
                    $descripcion = $instr['descripcion'] ?? null;
                    // La imagen puede venir como imagen_url o imagen
                    $imagen = $instr['imagen_url'] ?? ($instr['imagen'] ?? null);
                    // Inserta la instrucción
                    $insStmt->execute([
                        ':receta_id' => $newId,
                        ':orden' => $orden,
                        ':descripcion' => $descripcion,
                        ':imagen_url' => $imagen
                    ]);
                }
            }

            // Todo fue bien: confirma la transacción
            $this->conn->commit();
            // Devuelve el id de la nueva receta
            return $newId;
        } catch (PDOException $e) {
            // Si ocurre una excepción, deshace la transacción
            $this->conn->rollBack();
            // Guarda el mensaje de error en lastError
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // ------------------------------------------------------------
    // Actualizar una receta y reemplazar ingredientes + instrucciones
    // ------------------------------------------------------------
    public function update()
    {
        try {
            // Empieza transacción
            $this->conn->beginTransaction();

            // Consulta de actualización de la tabla recetas
            $query = "UPDATE " . $this->table_name . " SET titulo = :titulo, descripcion = :descripcion, tiempo_preparacion = :tiempo_preparacion, dificultad = :dificultad, categoria = :categoria, imagen_url = :imagen_url, destacada = :destacada, votos_positivos = :votos_positivos, votos_negativos = :votos_negativos WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Limpia y normaliza campos
            $this->titulo = htmlspecialchars(strip_tags($this->titulo));
            $this->descripcion = $this->descripcion ?? null;
            $this->tiempo_preparacion = $this->tiempo_preparacion ?? null;
            $this->dificultad = $this->dificultad ?? null;
            $this->categoria = $this->categoria ?? null;
            $this->imagen_url = $this->imagen_url ?? null;
            $this->destacada = $this->destacada ?? null;
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Vincula parámetros de la receta
            $stmt->bindParam(":titulo", $this->titulo);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":tiempo_preparacion", $this->tiempo_preparacion);
            $stmt->bindParam(":dificultad", $this->dificultad);
            $stmt->bindParam(":categoria", $this->categoria);
            $stmt->bindParam(":imagen_url", $this->imagen_url);
            $stmt->bindParam(":destacada", $this->destacada);

            // Conversión de votos a entero solo si están definidos
            $this->votos_positivos = isset($this->votos_positivos) ? (int)$this->votos_positivos : $this->votos_positivos;
            $this->votos_negativos = isset($this->votos_negativos) ? (int)$this->votos_negativos : $this->votos_negativos;

            $stmt->bindParam(":votos_positivos", $this->votos_positivos);
            $stmt->bindParam(":votos_negativos", $this->votos_negativos);
            $stmt->bindParam(":id", $this->id);

            // Ejecuta el UPDATE de la receta
            if (!$stmt->execute()) {
                // Si falla, revierte
                $this->conn->rollBack();
                return false;
            }

            // --------------------------------
            // Reemplazar ingredientes existentes
            // --------------------------------

            // Primero borra todos los ingredientes actuales de esa receta
            $delIng = $this->conn->prepare("DELETE FROM receta_ingredientes WHERE receta_id = :receta_id");
            $delIng->execute([':receta_id' => $this->id]);

            // Luego inserta los nuevos ingredientes (si vienen)
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

            // --------------------------------
            // Reemplazar instrucciones existentes
            // --------------------------------

            // Borra todas las instrucciones actuales de esa receta
            $delInstr = $this->conn->prepare("DELETE FROM receta_instrucciones WHERE receta_id = :receta_id");
            $delInstr->execute([':receta_id' => $this->id]);

            // Inserta las nuevas instrucciones si existen
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

            // Si todo fue bien, confirma la transacción
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            // En caso de excepción, revierte la transacción
            $this->conn->rollBack();
            // Guarda el error
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // ------------------------------------------------------------
    // Eliminar una receta (las relaciones se borran por ON DELETE CASCADE)
    // ------------------------------------------------------------
    public function delete($id)
    {
        // Consulta para borrar la receta principal
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        // Vincula el ID
        $stmt->bindParam(":id", $id);
        // Ejecuta y devuelve true/false
        return $stmt->execute();
    }

    // ------------------------------------------------------------
    // Métodos privados auxiliares para obtener relaciones
    // ------------------------------------------------------------

    // Obtener ingredientes de una receta por su id
    private function getIngredientesByRecetaId($recetaId)
    {
        // Consulta a la tabla relación receta_ingredientes
        $q = "SELECT ingrediente_id, cantidad, unidad FROM receta_ingredientes WHERE receta_id = :receta_id ORDER BY ingrediente_id";
        $s = $this->conn->prepare($q);
        $s->bindParam(':receta_id', $recetaId);
        $s->execute();

        // Obtenemos filas simples y para cada una adjuntamos el objeto ingrediente
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) return [];

        $ingredienteModel = new Ingrediente($this->conn);
        $results = [];
        foreach ($rows as $r) {
            $ingredienteId = $r['ingrediente_id'];
            // intentamos obtener el objeto ingrediente por id
            $ingredienteObj = $ingredienteModel->getById($ingredienteId);
            // adjuntamos la representación del ingrediente (o null si no existe)
            $r['ingrediente'] = $ingredienteObj ? $ingredienteObj : null;
            $results[] = $r;
        }

        return $results;
    }

    // Obtener instrucciones de una receta por su id
    private function getInstruccionesByRecetaId($recetaId)
    {
        // Consulta a la tabla receta_instrucciones
        $q = "SELECT orden, descripcion, imagen_url FROM receta_instrucciones WHERE receta_id = :receta_id ORDER BY orden";
        $s = $this->conn->prepare($q);
        $s->bindParam(':receta_id', $recetaId);
        $s->execute();
        // Devuelve todas las instrucciones ordenadas por "orden"
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUsuarioById($usuarioId)
    {
        if (!$usuarioId) return null;

        // Usar el modelo Usuario existente
        $usuarioModel = new Usuario($this->conn);
        return $usuarioModel->getById($usuarioId);
    }
}
