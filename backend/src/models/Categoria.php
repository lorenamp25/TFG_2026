<?php

class Categoria {
    private $conn;
    private $table_name = "categorias";

    public $id;
    public $nombre;

    public function __construct($db) {
        $this->conn = $db;
    }

}