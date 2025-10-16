<?php

class Comentario {
    private $conn;
    private $table_name = "comentarios";

    public $id;
    public $receta_id;
    public $usuario_id;
    public $contenido;
    public $puntuacion;
    public $fecha_creacion;

    public function __construct($db) {
        $this->conn = $db;
    }

}