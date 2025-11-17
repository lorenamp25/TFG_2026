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
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    icono VARCHAR(10) NOT NULL
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
    usuario_id INTEGER REFERENCES usuarios(id),
    votos_positivos INTEGER DEFAULT 0,
    votos_negativos INTEGER DEFAULT 0
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
        // Ensure new columns exist on existing databases (safe ALTER)
        $sql .= "\n-- Add votes columns if missing\nALTER TABLE recetas ADD COLUMN IF NOT EXISTS votos_positivos INTEGER DEFAULT 0;\nALTER TABLE recetas ADD COLUMN IF NOT EXISTS votos_negativos INTEGER DEFAULT 0;\n";

        try {
            $this->conn->exec($sql);
                        return true;
                } catch (PDOException $e) {
                        echo json_encode(["error" => "Error creating tables: " . $e->getMessage()]);
                        return false;
                }
    }
    public function crearDatosDummy() {
        
        try {
            $this->conn->beginTransaction();

            // --- Categories ---
            $categories = [
                ['nombre' => 'Entrantes', 'descripcion' => 'Aperitivos y entrantes', 'icono' => '🥗'],
                ['nombre' => 'Platos principales', 'descripcion' => 'Platos fuertes', 'icono' => '🍲'],
                ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres', 'icono' => '🍰'],
                ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'icono' => '🍹']
            ];
            $catIds = [];
            $selectCat = $this->conn->prepare("SELECT id FROM categorias WHERE nombre = :nombre LIMIT 1");
            $insertCat = $this->conn->prepare("INSERT INTO categorias (nombre, descripcion, icono) VALUES (:nombre, :descripcion, :icono) RETURNING id");
            foreach ($categories as $c) {
                $selectCat->execute([':nombre' => $c['nombre']]);
                $id = $selectCat->fetchColumn();
                if (!$id) {
                    $insertCat->execute([':nombre' => $c['nombre'], ':descripcion' => $c['descripcion'], ':icono' => $c['icono']]);
                    $id = $insertCat->fetchColumn();
                }
                $catIds[] = $id;
            }

            // --- Users (3) ---
            $users = [
                ['nickname' => 'lorena', 'nombre' => 'Lorena', 'apellido' => 'Gómez', 'email' => 'lorena@example.com', 'password' => password_hash('password1', PASSWORD_DEFAULT)],
                ['nickname' => 'carlos', 'nombre' => 'Carlos', 'apellido' => 'Perez', 'email' => 'carlos@example.com', 'password' => password_hash('password2', PASSWORD_DEFAULT)],
                ['nickname' => 'ana', 'nombre' => 'Ana', 'apellido' => 'Diaz', 'email' => 'ana@example.com', 'password' => password_hash('password3', PASSWORD_DEFAULT)]
            ];
            $userIds = [];
            $selectUser = $this->conn->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
            $insertUser = $this->conn->prepare("INSERT INTO usuarios (nickname, nombre, apellido, email, password, puntuacion) VALUES (:nickname, :nombre, :apellido, :email, :password, :puntuacion) RETURNING id");
            foreach ($users as $u) {
                $selectUser->execute([':email' => $u['email']]);
                $id = $selectUser->fetchColumn();
                if (!$id) {
                    $insertUser->execute([':nickname' => $u['nickname'], ':nombre' => $u['nombre'], ':apellido' => $u['apellido'], ':email' => $u['email'], ':password' => $u['password'], ':puntuacion' => 0]);
                    $id = $insertUser->fetchColumn();
                }
                $userIds[] = $id;
            }

            // --- Ingredientes (20) ---
            $ingredientes = [
                'Harina','Azúcar','Sal','Pimienta','Aceite de oliva','Huevos','Leche','Mantequilla','Ajo','Cebolla',
                'Tomate','Queso','Pollo','Carne de vaca','Perejil','Albahaca','Arroz','Pasta','Patata','Zanahoria'
            ];
            $ingredienteIds = [];
            $selectIng = $this->conn->prepare("SELECT id FROM ingredientes WHERE nombre = :nombre LIMIT 1");
            $insertIng = $this->conn->prepare("INSERT INTO ingredientes (nombre) VALUES (:nombre) RETURNING id");
            foreach ($ingredientes as $nombre) {
                $selectIng->execute([':nombre' => $nombre]);
                $id = $selectIng->fetchColumn();
                if (!$id) {
                    $insertIng->execute([':nombre' => $nombre]);
                    $id = $insertIng->fetchColumn();
                }
                $ingredienteIds[] = $id;
            }

            // --- Recetas (10) with ingredientes and instrucciones ---
            $titulos = [
                'Tarta de manzana','Pollo al horno','Pasta con salsa de tomate','Arroz con verduras','Ensalada mediterránea',
                'Sopa de pollo','Patatas al horno','Tortilla española','Galletas de mantequilla','Pasta al pesto'
            ];

            $insertReceta = $this->conn->prepare("INSERT INTO recetas (titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id, destacada, votos_positivos, votos_negativos) VALUES (:titulo, :descripcion, :tiempo_preparacion, :dificultad, :categoria, :imagen_url, :usuario_id, :destacada, :votos_positivos, :votos_negativos) RETURNING id");
            $insertRecIng = $this->conn->prepare("INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (:receta_id, :ingrediente_id, :cantidad, :unidad)");
            $insertInstr = $this->conn->prepare("INSERT INTO receta_instrucciones (receta_id, orden, descripcion, imagen_url) VALUES (:receta_id, :orden, :descripcion, :imagen_url)");

            foreach ($titulos as $i => $titulo) {
                $descripcion = "Deliciosa receta de $titulo.";
                $tiempo = rand(10, 120);
                $diffOptions = ['fácil','media','alta'];
                $dificultad = $diffOptions[array_rand($diffOptions)];
                $categoria = $catIds[array_rand($catIds)];
                $imagen = null;
                $usuario_id = $userIds[array_rand($userIds)];
                $destacada = (rand(0,9) === 0) ? true : false;

                $insertReceta->execute([
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion,
                    ':tiempo_preparacion' => $tiempo,
                    ':dificultad' => $dificultad,
                    ':categoria' => $categoria,
                    ':imagen_url' => $imagen,
                    ':usuario_id' => $usuario_id,
                    ':destacada' => $destacada,
                    ':votos_positivos' => rand(0,50),
                    ':votos_negativos' => rand(0,20)
                ]);
                $recetaId = $insertReceta->fetchColumn();

                // add 3-6 ingredientes
                $numIng = rand(3,6);
                $used = [];
                for ($k=0;$k<$numIng;$k++) {
                    $idx = $ingredienteIds[array_rand($ingredienteIds)];
                    if (in_array($idx,$used)) continue;
                    $used[] = $idx;
                    $cantidad = rand(1,500);
                    $unidad = (rand(0,1)===0)?'g':'ml';
                    $insertRecIng->execute([':receta_id'=>$recetaId,':ingrediente_id'=>$idx,':cantidad'=>$cantidad,':unidad'=>$unidad]);
                }

                // add 2-5 instrucciones
                $numInstr = rand(2,5);
                for ($s=1;$s<=$numInstr;$s++) {
                    $desc = "Paso $s para preparar $titulo.";
                    $insertInstr->execute([':receta_id'=>$recetaId,':orden'=>$s,':descripcion'=>$desc,':imagen_url'=>null]);
                }
            }

            // --- Comentarios: crear algunos comentarios aleatorios ---
            $insertComentario = $this->conn->prepare("INSERT INTO comentarios (receta_id, usuario_id, contenido, puntuacion) VALUES (:receta_id, :usuario_id, :contenido, :puntuacion)");
            // fetch some recipe ids
            $rstmt = $this->conn->query("SELECT id FROM recetas ORDER BY id DESC LIMIT 10");
            $rids = $rstmt->fetchAll(PDO::FETCH_COLUMN);
            $sampleComments = ["Deliciosa!", "Me gustó mucho", "Fácil de preparar", "No me convenció", "Perfecta para cenar"];
            foreach ($rids as $rid) {
                $num = rand(0,3);
                for ($c=0;$c<$num;$c++) {
                    $insertComentario->execute([':receta_id'=>$rid, ':usuario_id'=>$userIds[array_rand($userIds)], ':contenido'=>$sampleComments[array_rand($sampleComments)], ':puntuacion'=>rand(1,5)]);
                }
            }

            $this->conn->commit();





            return true;
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error inserting dummy data: " . $e->getMessage()]);
            return false;
        }

        

    }
}