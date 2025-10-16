<?php

class Ingrediente {
    private $conn;
    private $table_name = "ingredientes";

    public $id;
    public $nombre;

    public function __construct($db) {
        $this->conn = $db;
    }

}