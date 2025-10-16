<?php

class RecetaInstruccion {
    private $conn;
    private $table_name = "receta_instrucciones";

    public $receta_id;
    public $orden;
    public $descripcion;
    public $imagen_url;

    public function __construct($db) {
        $this->conn = $db;
    }

}