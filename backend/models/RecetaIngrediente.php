<?php

// Modelo que representa la tabla de relación "receta_ingredientes"
class RecetaIngrediente {
    // Conexión a la base de datos (PDO)
    private $conn;
    // Nombre de la tabla asociada en la base de datos
    private $table_name = "receta_ingredientes";

    // Clave foránea a la receta
    public $receta_id;
    // Clave foránea al ingrediente
    public $ingrediente_id;
    // Cantidad de ese ingrediente en la receta (ej: 200)
    public $cantidad;
    // Unidad de medida (ej: "g", "ml", "cucharadas")
    public $unidad;

    // Constructor: recibe el objeto de conexión y lo guarda
    public function __construct($db) {
        $this->conn = $db;
    }

}
