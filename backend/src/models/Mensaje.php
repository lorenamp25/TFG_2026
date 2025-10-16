<?php

class Mensaje {
    private $conn;
    private $table_name = "mensajes";

    public $id;
    public $remitente;
    public $destinatario;
    public $asunto;
    public $contenido;
    public $fecha_envio;
    public $leido;

    public function __construct($db) {
        $this->conn = $db;
    }

}