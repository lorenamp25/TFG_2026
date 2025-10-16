<?php

class Receta {
    private $conn;
    private $table_name = "recetas";

    public $id;
    public $titulo;
    public $ingredientes;
    public $instrucciones;
    public $tiempo_preparacion;
    public $dificultad;
    public $categoria;
    public $imagen_url;
    public $usuario_id;

    public function __construct($db) {
        $this->conn = $db;
    }

}