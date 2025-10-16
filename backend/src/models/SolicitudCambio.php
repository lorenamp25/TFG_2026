<?php

class SolicitudCambio {
    private $conn;
    private $table_name = "solicitudes_cambio";

    public $id;
    public $usuario_id;
    public $receta_id;
    public $descripcion;
    public $estado; // 'pendiente', 'aprobada', 'rechazada'
    public $fecha_solicitud;
    public $fecha_resolucion;

    public function __construct($db) {
        $this->conn = $db;
    }

}