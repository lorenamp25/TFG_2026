<?php

// Modelo que representa la tabla "receta_instrucciones"
class RecetaInstruccion {
    // Conexión a la base de datos (objeto PDO)
    private $conn;
    // Nombre de la tabla asociada en la BD
    private $table_name = "receta_instrucciones";

    // ID de la receta a la que pertenece la instrucción (clave foránea)
    public $receta_id;
    // Número de orden del paso dentro de la receta (Paso 1, paso 2...)
    public $orden;
    // Texto descriptivo del paso
    public $descripcion;
    // URL opcional de una imagen asociada al paso
    public $imagen_url;

    // Constructor: recibe la conexión y la guarda para usarla en consultas
    public function __construct($db) {
        $this->conn = $db;
    }

}
