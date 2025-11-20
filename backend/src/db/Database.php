<?php

// Clase encargada de manejar la conexión a la base de datos y la creación de tablas/datos de ejemplo
class Database {
    // Propiedades privadas para los datos de conexión
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;

    // Propiedad pública donde se guardará la conexión PDO
    public $conn;

    // Constructor: se ejecuta al crear un objeto Database
    public function __construct() {
        // Leer variables de entorno que define docker-compose (con valores por defecto)
        // Si existe la variable de entorno DB_HOST la usa, si no, usa 'db'
        $this->host = getenv('DB_HOST') ?: 'db';
        // Si existe DB_PORT la usa, si no, 5432 (por defecto en PostgreSQL)
        $this->port = getenv('DB_PORT') ?: '5432';
        // Nombre de la base de datos (DB_NAME o 'receta' por defecto)
        $this->db_name = getenv('DB_NAME') ?: 'receta';
        // Usuario de la base de datos (DB_USER o 'receta' por defecto)
        $this->username = getenv('DB_USER') ?: 'receta';
        // Contraseña de la BD (DB_PASS o '1234' por defecto)
        $this->password = getenv('DB_PASS') ?: '1234';
    }

    // Método para obtener la conexión a la base de datos
    public function getConnection() {
        // Inicializa la conexión como null
        $this->conn = null;

        try {
            // DSN para PostgreSQL: indica host, puerto y nombre de la base de datos
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";

            // Crea un nuevo objeto PDO usando el DSN, usuario y contraseña
            // Además configura:
            // - Que los errores se lancen como excepciones
            // - Que el modo de fetch por defecto sea un array asociativo
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Establece el juego de caracteres a UTF-8
            $this->conn->exec("SET NAMES 'utf8'");
        } catch(PDOException $exception) {
            // Si hay algún error de conexión, lo muestra en formato JSON
            echo json_encode(["error" => "Connection error: " . $exception->getMessage()]);
        }

        // Devuelve el objeto de conexión (o null si falló)
        return $this->conn;
    }

    // Crear las tablas necesarias (si no existen)
    public function createTables() {
        // Bloque de SQL (heredoc) con la definición de todas las tablas
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
        // Añade al string SQL unas sentencias ALTER TABLE para asegurar
        // que las columnas de votos existen aunque la tabla ya estuviera creada
        $sql .= "\n-- Add votes columns if missing\nALTER TABLE recetas ADD COLUMN IF NOT EXISTS votos_positivos INTEGER DEFAULT 0;\nALTER TABLE recetas ADD COLUMN IF NOT EXISTS votos_negativos INTEGER DEFAULT 0;\n";

        try {
            // Ejecuta todo el bloque SQL (creación/alteración de tablas)
            $this->conn->exec($sql);
            // Si no hay errores, devuelve true
            return true;
        } catch (PDOException $e) {
            // Si algo falla, muestra el error en formato JSON
            echo json_encode(["error" => "Error creating tables: " . $e->getMessage()]);
            // Y devuelve false
            return false;
        }
    }

    // Crear datos de ejemplo (dummy) en la base de datos
    public function crearDatosDummy() {
        
        try {
            // Inicia una transacción para que todos los inserts sean atómicos
            $this->conn->beginTransaction();

            // --- Categories ---
            // Array con categorías de ejemplo
            $categories = [
                ['nombre' => 'Entrantes', 'descripcion' => 'Aperitivos y entrantes', 'icono' => '🥗'],
                ['nombre' => 'Platos principales', 'descripcion' => 'Platos fuertes', 'icono' => '🍲'],
                ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres', 'icono' => '🍰'],
                ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'icono' => '🍹']
            ];
            // Array para guardar los IDs de categorías
            $catIds = [];
            // Consulta para buscar categoría por nombre
            $selectCat = $this->conn->prepare("SELECT id FROM categorias WHERE nombre = :nombre LIMIT 1");
            // Inserta categoría nueva si no existe
            $insertCat = $this->conn->prepare("INSERT INTO categorias (nombre, descripcion, icono) VALUES (:nombre, :descripcion, :icono) RETURNING id");
            // Recorre cada categoría
            foreach ($categories as $c) {
                // Comprueba si ya existe esa categoría
                $selectCat->execute([':nombre' => $c['nombre']]);
                $id = $selectCat->fetchColumn();
                // Si no existe, la inserta y obtiene su nuevo ID
                if (!$id) {
                    $insertCat->execute([':nombre' => $c['nombre'], ':descripcion' => $c['descripcion'], ':icono' => $c['icono']]);
                    $id = $insertCat->fetchColumn();
                }
                // Guarda el ID en el array de categorías
                $catIds[] = $id;
            }

            // --- Users (3) ---
            // Lista de usuarios de prueba
            $users = [
                ['nickname' => 'lorena', 'nombre' => 'Lorena', 'apellido' => 'Gómez', 'email' => 'lorena@example.com', 'password' => password_hash('password1', PASSWORD_DEFAULT)],
                ['nickname' => 'carlos', 'nombre' => 'Carlos', 'apellido' => 'Perez', 'email' => 'carlos@example.com', 'password' => password_hash('password2', PASSWORD_DEFAULT)],
                ['nickname' => 'ana', 'nombre' => 'Ana', 'apellido' => 'Diaz', 'email' => 'ana@example.com', 'password' => password_hash('password3', PASSWORD_DEFAULT)]
            ];
            // Array para IDs de usuarios
            $userIds = [];
            // Consulta para buscar usuario por email
            $selectUser = $this->conn->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
            // Inserta usuario si no existe
            $insertUser = $this->conn->prepare("INSERT INTO usuarios (nickname, nombre, apellido, email, password, puntuacion) VALUES (:nickname, :nombre, :apellido, :email, :password, :puntuacion) RETURNING id");
            foreach ($users as $u) {
                // Comprueba si ya existe este email
                $selectUser->execute([':email' => $u['email']]);
                $id = $selectUser->fetchColumn();
                // Si no existe, lo inserta
                if (!$id) {
                    $insertUser->execute([':nickname' => $u['nickname'], ':nombre' => $u['nombre'], ':apellido' => $u['apellido'], ':email' => $u['email'], ':password' => $u['password'], ':puntuacion' => 0]);
                    $id = $insertUser->fetchColumn();
                }
                // Guarda el ID en el array de usuarios
                $userIds[] = $id;
            }

            // --- Ingredientes (20) ---
            // Lista de nombres de ingredientes de ejemplo
            $ingredientes = [
                'Harina','Azúcar','Sal','Pimienta','Aceite de oliva','Huevos','Leche','Mantequilla','Ajo','Cebolla',
                'Tomate','Queso','Pollo','Carne de vaca','Perejil','Albahaca','Arroz','Pasta','Patata','Zanahoria'
            ];
            // Array con IDs de ingredientes
            $ingredienteIds = [];
            // Consulta para ver si existe ya un ingrediente
            $selectIng = $this->conn->prepare("SELECT id FROM ingredientes WHERE nombre = :nombre LIMIT 1");
            // Inserta ingrediente si no existe
            $insertIng = $this->conn->prepare("INSERT INTO ingredientes (nombre) VALUES (:nombre) RETURNING id");
            foreach ($ingredientes as $nombre) {
                // Comprueba si ya existe ese ingrediente
                $selectIng->execute([':nombre' => $nombre]);
                $id = $selectIng->fetchColumn();
                // Si no existe, lo inserta
                if (!$id) {
                    $insertIng->execute([':nombre' => $nombre]);
                    $id = $insertIng->fetchColumn();
                }
                // Guarda el ID
                $ingredienteIds[] = $id;
            }

            // --- Recetas (10) with ingredientes and instrucciones ---
            // Lista de títulos de recetas de ejemplo
            $titulos = [
                'Tarta de manzana','Pollo al horno','Pasta con salsa de tomate','Arroz con verduras','Ensalada mediterránea',
                'Sopa de pollo','Patatas al horno','Tortilla española','Galletas de mantequilla','Pasta al pesto'
            ];

            // Prepare para insertar recetas
            $insertReceta = $this->conn->prepare("INSERT INTO recetas (titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id, destacada, votos_positivos, votos_negativos) VALUES (:titulo, :descripcion, :tiempo_preparacion, :dificultad, :categoria, :imagen_url, :usuario_id, :destacada, :votos_positivos, :votos_negativos) RETURNING id");
            // Prepare para insertar relación receta-ingredientes
            $insertRecIng = $this->conn->prepare("INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (:receta_id, :ingrediente_id, :cantidad, :unidad)");
            // Prepare para insertar instrucciones de receta
            $insertInstr = $this->conn->prepare("INSERT INTO receta_instrucciones (receta_id, orden, descripcion, imagen_url) VALUES (:receta_id, :orden, :descripcion, :imagen_url)");

            // Recorre cada título de receta
            foreach ($titulos as $i => $titulo) {
                // Genera una descripción simple en base al título
                $descripcion = "Deliciosa receta de $titulo.";
                // Tiempo de preparación aleatorio entre 10 y 120 minutos
                $tiempo = rand(10, 120);
                // Posibles niveles de dificultad
                $diffOptions = ['fácil','media','alta'];
                // Escoge dificultad aleatoria
                $dificultad = $diffOptions[array_rand($diffOptions)];
                // Categoría aleatoria de las creadas arriba
                $categoria = $catIds[array_rand($catIds)];
                // Por ahora sin imagen
                $imagen = null;
                // Usuario autor aleatorio
                $usuario_id = $userIds[array_rand($userIds)];
                // Marca algunas como destacadas aleatoriamente
                $destacada = (rand(0,9) === 0) ? true : false;

                // Inserta la receta
                $insertReceta->execute([
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion,
                    ':tiempo_preparacion' => $tiempo,
                    ':dificultad' => $dificultad,
                    ':categoria' => $categoria,
                    ':imagen_url' => $imagen,
                    ':usuario_id' => $usuario_id,
                    ':votos_positivos' => rand(0,50),
                    ':votos_negativos' => rand(0,20)
                ]);
                // Obtiene el ID de la receta recién insertada
                $recetaId = $insertReceta->fetchColumn();

                // add 3-6 ingredientes
                // Genera entre 3 y 6 ingredientes asociados a esta receta
                $numIng = rand(3,6);
                // Array para evitar repetir el mismo ingrediente
                $used = [];
                for ($k=0;$k<$numIng;$k++) {
                    // Escoge un ingrediente aleatorio
                    $idx = $ingredienteIds[array_rand($ingredienteIds)];
                    // Si ya se usó ese ingrediente en esta receta, lo salta
                    if (in_array($idx,$used)) continue;
                    // Lo marca como usado
                    $used[] = $idx;
                    // Cantidad aleatoria
                    $cantidad = rand(1,500);
                    // Unidad aleatoria entre gramos o mililitros
                    $unidad = (rand(0,1)===0)?'g':'ml';
                    // Inserta la relación receta-ingrediente
                    $insertRecIng->execute([':receta_id'=>$recetaId,':ingrediente_id'=>$idx,':cantidad'=>$cantidad,':unidad'=>$unidad]);
                }

                // add 2-5 instrucciones
                // Define entre 2 y 5 pasos para la receta
                $numInstr = rand(2,5);
                for ($s=1;$s<=$numInstr;$s++) {
                    // Descripción del paso actual
                    $desc = "Paso $s para preparar $titulo.";
                    // Inserta la instrucción
                    $insertInstr->execute([':receta_id'=>$recetaId,':orden'=>$s,':descripcion'=>$desc,':imagen_url'=>null]);
                }
            }

            // --- Comentarios: crear algunos comentarios aleatorios ---
            // Prepare para insertar comentarios
            $insertComentario = $this->conn->prepare("INSERT INTO comentarios (receta_id, usuario_id, contenido, puntuacion) VALUES (:receta_id, :usuario_id, :contenido, :puntuacion)");
            // fetch some recipe ids
            // Consulta para obtener IDs de las últimas 10 recetas
            $rstmt = $this->conn->query("SELECT id FROM recetas ORDER BY id DESC LIMIT 10");
            // Guarda los IDs de esas recetas
            $rids = $rstmt->fetchAll(PDO::FETCH_COLUMN);
            // Comentarios de ejemplo
            $sampleComments = ["Deliciosa!", "Me gustó mucho", "Fácil de preparar", "No me convenció", "Perfecta para cenar"];
            // Recorre las recetas
            foreach ($rids as $rid) {
                // Número aleatorio de comentarios para esta receta
                $num = rand(0,3);
                for ($c=0;$c<$num;$c++) {
                    // Inserta comentario con usuario aleatorio, comentario aleatorio y puntuación aleatoria
                    $insertComentario->execute([':receta_id'=>$rid, ':usuario_id'=>$userIds[array_rand($userIds)], ':contenido'=>$sampleComments[array_rand($sampleComments)], ':puntuacion'=>rand(1,5)]);
                }
            }

            // Confirma todos los cambios de la transacción
            $this->conn->commit();

            // Si todo fue bien, devuelve true
            return true;
        } catch (PDOException $e) {
            // Si hay error, lo muestra en JSON
            echo json_encode(["error" => "Error inserting dummy data: " . $e->getMessage()]);
            // Revienta devolviendo false
            return false;
        }

    }
}
