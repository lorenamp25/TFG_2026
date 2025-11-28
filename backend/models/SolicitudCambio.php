<?php

// Modelo que representa la tabla "solicitudes_cambio" en la base de datos
class SolicitudCambio {
    // Conexión a la base de datos (objeto PDO)
    private $conn;
    // Nombre de la tabla asociada
    private $table_name = "solicitudes_cambio";

    // ID único de la solicitud (PRIMARY KEY)
    public $id;
    // ID del usuario que hizo la solicitud (clave foránea hacia usuarios)
    public $usuario_id;
    // ID de la receta sobre la que se solicita el cambio (clave foránea)
    public $receta_id;
    // Descripción del cambio solicitado
    public $descripcion;
    // Estado de la solicitud: puede ser 'pendiente', 'aprobada' o 'rechazada'
    public $estado;
    // Fecha de creación de la solicitud (TIMESTAMP)
    public $fecha_solicitud;
    // Fecha en la que se resolvió la solicitud (puede ser null)
    public $fecha_resolucion;

    // Constructor: recibe el objeto de conexión y lo guarda
    public function __construct($db) {
        $this->conn = $db;
    }

}
