<?php

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nickname;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $fecha_nacimiento;
    public $puntuacion;

    public function __construct($db) {
        $this->conn = $db;
    }


    

}

