<?php

class RecetaIngrediente {
    private $conn;
    private $table_name = "receta_ingredientes";

    public $receta_id;
    public $ingrediente_id;
    public $cantidad;
    public $unidad;

    public function __construct($db) {
        $this->conn = $db;
    }

}