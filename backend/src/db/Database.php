<?php

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Leer variables de entorno que define docker-compose (con valores por defecto)
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->port = getenv('DB_PORT') ?: '5432';
        $this->db_name = getenv('DB_NAME') ?: 'receta';
        $this->username = getenv('DB_USER') ?: 'receta';
        $this->password = getenv('DB_PASS') ?: '1234';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            // DSN para PostgreSQL
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";

            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $this->conn->exec("SET NAMES 'utf8'");
        } catch(PDOException $exception) {
            echo json_encode(["error" => "Connection error: " . $exception->getMessage()]);
        }

        return $this->conn;
    }

        // Crear las tablas necesarias (si no existen)
        public function createTables() {
                $sql = <<<SQL
-- Categorías
CREATE TABLE IF NOT EXISTS categorias (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

-- Ingredientes
CREATE TABLE IF NOT EXISTS ingredientes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

-- Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nickname VARCHAR(100) UNIQUE,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    fecha_nacimiento DATE,
    puntuacion INTEGER DEFAULT 0
);

-- Recetas
CREATE TABLE IF NOT EXISTS recetas (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tiempo_preparacion INTEGER,
    dificultad VARCHAR(50),
    categoria INTEGER REFERENCES categorias(id),
    imagen_url VARCHAR(1024),
    usuario_id INTEGER REFERENCES usuarios(id)
);

-- Receta ingredientes (relación)
CREATE TABLE IF NOT EXISTS receta_ingredientes (
    receta_id INTEGER REFERENCES recetas(id) ON DELETE CASCADE,
    ingrediente_id INTEGER REFERENCES ingredientes(id) ON DELETE CASCADE,
    cantidad VARCHAR(64),
    unidad VARCHAR(64),
    PRIMARY KEY (receta_id, ingrediente_id)
);

-- Instrucciones de receta
CREATE TABLE IF NOT EXISTS receta_instrucciones (
    receta_id INTEGER REFERENCES recetas(id) ON DELETE CASCADE,
    orden INTEGER,
    descripcion TEXT,
    imagen_url VARCHAR(1024),
    PRIMARY KEY (receta_id, orden)
);

-- Comentarios
CREATE TABLE IF NOT EXISTS comentarios (
    id SERIAL PRIMARY KEY,
    receta_id INTEGER REFERENCES recetas(id) ON DELETE CASCADE,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
    contenido TEXT NOT NULL,
    puntuacion INTEGER,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Mensajes
CREATE TABLE IF NOT EXISTS mensajes (
    id SERIAL PRIMARY KEY,
    remitente INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
    destinatario INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
    asunto VARCHAR(255),
    contenido TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE
);

-- Solicitudes de cambio
CREATE TABLE IF NOT EXISTS solicitudes_cambio (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
    receta_id INTEGER REFERENCES recetas(id) ON DELETE SET NULL,
    descripcion TEXT,
    estado VARCHAR(20) DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion TIMESTAMP
);

SQL;

                try {
                        $this->conn->exec($sql);
                        return true;
                } catch (PDOException $e) {
                        echo json_encode(["error" => "Error creating tables: " . $e->getMessage()]);
                        return false;
                }
        }
}