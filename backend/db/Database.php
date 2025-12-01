<?php

// Clase encargada de manejar la conexión a la base de datos y la creación de tablas/datos de ejemplo
class Database
{
    // Propiedades privadas para los datos de conexión
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;

    // Propiedad pública donde se guardará la conexión PDO
    public $conn;

    // Constructor: se ejecuta al crear un objeto Database
    public function __construct()
    {
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
    public function getConnection()
    {
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
        } catch (PDOException $exception) {
            // Si hay algún error de conexión, lo muestra en formato JSON
            echo json_encode(["error" => "Connection error: " . $exception->getMessage()]);
        }

        // Devuelve el objeto de conexión (o null si falló)
        return $this->conn;
    }

    // Crear las tablas necesarias (si no existen)
    public function createTables()
    {
        // Bloque de SQL (heredoc) con la definición de todas las tablas
        $sql = "
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
    puntuacion INTEGER DEFAULT 0,
    es_admin BOOLEAN DEFAULT FALSE
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
    votos_negativos INTEGER DEFAULT 0,
    destacada BOOLEAN DEFAULT FALSE
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
";

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
    public function crearDatosDummy()
    {
        try {
            // Verificar si ya existen datos en la tabla 'categorias'
            $checkCategorias = $this->conn->query("SELECT COUNT(*) FROM categorias");
            if ($checkCategorias->fetchColumn() > 0) {
                // La tabla 'categorias' ya contiene datos."
                return false;
            }

            // Inicia una transacción para que todos los inserts sean atómicos
            $this->conn->beginTransaction();

            // --- Categorías coherentes ---
            $categories = [
                ['nombre' => 'Entrantes', 'descripcion' => 'Aperitivos y entrantes ligeros', 'icono' => '🥗'],
                ['nombre' => 'Ensaladas', 'descripcion' => 'Ensaladas frescas y saludables', 'icono' => '🥬'],
                ['nombre' => 'Sopas', 'descripcion' => 'Sopas y cremas calientes', 'icono' => '🍲'],
                ['nombre' => 'Carnes', 'descripcion' => 'Platos principales de carne', 'icono' => '🥩'],
                ['nombre' => 'Pescados', 'descripcion' => 'Platos de pescado y marisco', 'icono' => '🐟'],
                ['nombre' => 'Vegetariano', 'descripcion' => 'Platos sin productos animales', 'icono' => '🌱'],
                ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres deliciosos', 'icono' => '🍰']
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
                $catIds[$c['nombre']] = $id; // Guardamos con clave nombre para acceso fácil
            }

            // --- Usuarios ---
            $users = [
                ['nickname' => 'chef_lorena', 'nombre' => 'Lorena', 'apellido' => 'Gómez', 'email' => 'lorena@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT), 'es_admin' => 1],
                ['nickname' => 'carlos_cocina', 'nombre' => 'Carlos', 'apellido' => 'Perez', 'email' => 'carlos@gmail.com', 'password' => password_hash('123456', PASSWORD_DEFAULT), 'es_admin' => 0],
                ['nickname' => 'ana_dulces', 'nombre' => 'Ana', 'apellido' => 'Diaz', 'email' => 'ana@gmail.com', 'password' => password_hash('123456', PASSWORD_DEFAULT), 'es_admin' => 0],
                ['nickname' => 'maria_healthy', 'nombre' => 'María', 'apellido' => 'Lopez', 'email' => 'maria@gmail.com', 'password' => password_hash('123456', PASSWORD_DEFAULT), 'es_admin' => 0]
            ];

            $userIds = [];
            $selectUser = $this->conn->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
            $insertUser = $this->conn->prepare("INSERT INTO usuarios (nickname, nombre, apellido, email, password, es_admin, puntuacion) VALUES (:nickname, :nombre, :apellido, :email, :password, :es_admin, :puntuacion) RETURNING id");

            foreach ($users as $u) {
                $selectUser->execute([':email' => $u['email']]);
                $id = $selectUser->fetchColumn();
                if (!$id) {
                    $insertUser->execute([':nickname' => $u['nickname'], ':nombre' => $u['nombre'], ':apellido' => $u['apellido'], ':email' => $u['email'], ':password' => $u['password'], ':es_admin' => $u['es_admin'], ':puntuacion' => rand(50, 200)]);
                    $id = $insertUser->fetchColumn();
                }
                $userIds[] = $id;
            }

            // --- Ingredientes organizados por tipo ---
            $ingredientes = [
                // Harinas y granos
                'Harina de trigo',
                'Harina integral',
                'Arroz blanco',
                'Arroz integral',
                'Pasta',
                'Pan rallado',
                'Avena',
                // Lácteos
                'Leche',
                'Queso parmesano',
                'Queso mozzarella',
                'Queso cheddar',
                'Yogur natural',
                'Mantequilla',
                'Nata líquida',
                // Proteínas
                'Pechuga de pollo',
                'Filete de ternera',
                'Salmón',
                'Merluza',
                'Gambas',
                'Huevos',
                'Tofu',
                'Lentejas',
                // Verduras
                'Cebolla',
                'Ajo',
                'Tomate',
                'Zanahoria',
                'Pimiento rojo',
                'Pimiento verde',
                'Calabacín',
                'Berenjena',
                'Espinacas',
                'Lechuga',
                // Frutas
                'Limón',
                'Manzana',
                'Plátano',
                'Fresas',
                'Chocolate negro',
                'Azúcar',
                'Miel',
                'Canela',
                'Vainilla',
                // Condimentos y aceites
                'Aceite de oliva',
                'Vinagre',
                'Sal',
                'Pimienta',
                'Orégano',
                'Albahaca',
                'Perejil',
                'Romero'
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
                $ingredienteIds[$nombre] = $id; // Guardamos con clave nombre
            }

            // --- Recetas coherentes (20 recetas) ---
            $recetas = [
                // Entrantes
                [
                    'titulo' => 'Bruschetta de tomate y albahaca',
                    'descripcion' => 'Deliciosa bruschetta italiana con tomate fresco y albahaca',
                    'tiempo' => 16,
                    'dificultad' => 'fácil',
                    'categoria' => 'Entrantes',
                    'imagen_url'=>'uploads/recetas/Bruschettadetomateyalbahaca.jpg',
                    'ingredientes' => [
                        ['Pan de chapata', 8, 'rebanadas'],
                        ['Tomate', 4, 'unidades'],
                        ['Ajo', 2, 'dientes'],
                        ['Albahaca', 10, 'hojas'],
                        ['Aceite de oliva', 4, 'cucharadas'],
                        ['Sal', 1, 'pizca']
                    ],
                    'instrucciones' => [
                        'Cortar el pan en rebanadas y tostarlas ligeramente.',
                        'Picar el tomate y el ajo finamente.',
                        'Mezclar el tomate, ajo y albahaca con aceite de oliva y sal.',
                        'Colocar la mezcla sobre las rebanadas de pan y servir.'
                    ]
                ],
                [
                    'titulo' => 'Hummus de garbanzos',
                    'descripcion' => 'Crema de garbanzos al estilo mediterráneo',
                    'tiempo' => 20,
                    'dificultad' => 'fácil',
                    'categoria' => 'Entrantes',
                    'imagen_url'=>'uploads/recetas/Hummus de garbanzos.jpeg',
                    'ingredientes' => [
                        ['Garbanzos cocidos', 400, 'g'],
                        ['Aceite de oliva', 4, 'cucharadas'],
                        ['Limón', 1, 'unidad'],
                        ['Ajo', 2, 'dientes'],
                        ['Comino', 1, 'cucharadita'],
                        ['Sal', 1, 'pizca']
                    ],
                    'instrucciones' => [
                        'Escurrir los garbanzos y reservar el líquido.',
                        'Mezclar todos los ingredientes en la batidora.',
                        'Triturar hasta obtener una crema suave.',
                        'Añadir el líquido de los garbanzos si está muy espeso.',
                        'Servir con un chorrito de aceite de oliva por encima.'
                    ]
                ],

                // Ensaladas
                [
                    'titulo' => 'Ensalada César',
                    'descripcion' => 'Clásica ensalada César con pollo y aderezo cremoso',
                    'tiempo' => 25,
                    'dificultad' => 'media',
                    'categoria' => 'Ensaladas',
                    'imagen_url'=>'uploads/recetas/ensaladadequinoa.jpg',
                    'ingredientes' => [
                        ['Lechuga', 1, 'unidad'],
                        ['Pechuga de pollo', 2, 'unidades'],
                        ['Pan de chapata', 4, 'rebanadas'],
                        ['Queso parmesano', 50, 'g'],
                        ['Aceite de oliva', 3, 'cucharadas'],
                        ['Limón', 1, 'unidad'],
                        ['Ajo', 1, 'diente']
                    ],
                    'instrucciones' => [
                        'Cocinar el pollo a la plancha y cortar en tiras.',
                        'Cortar el pan en cubos y tostar en el horno.',
                        'Lavar y cortar la lechuga.',
                        'Preparar el aderezo con aceite, limón y ajo.',
                        'Mezclar todos los ingredientes y añadir el queso rallado.'
                    ]
                ],

                // Sopas
                [
                    'titulo' => 'Crema de calabacín',
                    'descripcion' => 'Sopa cremosa de calabacín ligera y saludable',
                    'tiempo' => 30,
                    'dificultad' => 'fácil',
                    'categoria' => 'Sopas',
                    'imagen_url'=>'uploads/recetas/cremadecalabacin.jpg',
                    'ingredientes' => [
                        ['Calabacín', 3, 'unidades'],
                        ['Cebolla', 1, 'unidad'],
                        ['Ajo', 2, 'dientes'],
                        ['Caldo de verduras', 500, 'ml'],
                        ['Nata líquida', 100, 'ml'],
                        ['Aceite de oliva', 2, 'cucharadas'],
                        ['Sal', 1, 'pizca']
                    ],
                    'instrucciones' => [
                        'Picar la cebolla y el ajo y sofreír en aceite.',
                        'Añadir el calabacín cortado y cocinar 5 minutos.',
                        'Añadir el caldo y cocinar 15 minutos.',
                        'Triturar hasta obtener una crema suave.',
                        'Añadir la nata y rectificar de sal.'
                    ]
                ],

                // Carnes
                [
                    'titulo' => 'Pollo al horno con patatas',
                    'descripcion' => 'Pollo asado con patatas y hierbas aromáticas',
                    'tiempo' => 80,
                    'dificultad' => 'media',
                    'categoria' => 'Carnes',
                    'imagen_url'=>'uploads/recetas/Polloalhornoconpatatas.jpg',
                    'ingredientes' => [
                        ['Pollo entero', 1, 'unidad'],
                        ['Patata', 6, 'unidades'],
                        ['Cebolla', 2, 'unidades'],
                        ['Ajo', 4, 'dientes'],
                        ['Romero', 2, 'ramas'],
                        ['Aceite de oliva', 4, 'cucharadas'],
                        ['Sal', 2, 'cucharaditas']
                    ],
                    'instrucciones' => [
                        'Precalentar el horno a 180°C.',
                        'Limpiar y salpimentar el pollo por dentro y por fuera.',
                        'Pelar y cortar las patatas en gajos.',
                        'Colocar el pollo y las patatas en una bandeja.',
                        'Añadir las hierbas y hornear durante 1 hora.'
                    ]
                ],

                [
                    'titulo' => 'Lasaña de carne',
                    'descripcion' => 'Lasaña tradicional con carne y bechamel',
                    'tiempo' => 90,
                    'dificultad' => 'alta',
                    'categoria' => 'Carnes',
                    'imagen_url'=>'uploads/recetas/lasaña.jpg',
                    'ingredientes' => [
                        ['Carne picada', 500, 'g'],
                        ['Láminas de lasaña', 12, 'unidades'],
                        ['Tomate', 4, 'unidades'],
                        ['Cebolla', 1, 'unidad'],
                        ['Queso mozzarella', 200, 'g'],
                        ['Queso parmesano', 100, 'g'],
                        ['Leche', 500, 'ml'],
                        ['Harina de trigo', 50, 'g'],
                        ['Mantequilla', 50, 'g']
                    ],
                    'instrucciones' => [
                        'Preparar la salsa de tomate con la carne.',
                        'Hacer la bechamel con mantequilla, harina y leche.',
                        'Montar capas: salsa, láminas, bechamel.',
                        'Repetir las capas y terminar con queso.',
                        'Hornear a 180°C durante 40 minutos.'
                    ]
                ],

                // Pescados
                [
                    'titulo' => 'Salmón a la plancha',
                    'descripcion' => 'Salmón fresco cocinado a la plancha con limón',
                    'tiempo' => 20,
                    'dificultad' => 'fácil',
                    'categoria' => 'Pescados',
                    'imagen_url'=>'uploads/recetas/salmon.jpg',
                    'ingredientes' => [
                        ['Salmón', 4, 'filetes'],
                        ['Limón', 2, 'unidades'],
                        ['Aceite de oliva', 2, 'cucharadas'],
                        ['Eneldo', 1, 'cucharadita'],
                        ['Sal', 1, 'pizca']
                    ],
                    'instrucciones' => [
                        'Sazonar el salmón con sal y eneldo.',
                        'Calentar la plancha con aceite.',
                        'Cocinar el salmón 4 minutos por cada lado.',
                        'Exprimir limón por encima antes de servir.'
                    ]
                ],

                // Vegetariano
                [
                    'titulo' => 'Risotto de champiñones',
                    'descripcion' => 'Risotto cremoso con champiñones y parmesano',
                    'tiempo' => 40,
                    'dificultad' => 'media',
                    'categoria' => 'Vegetariano',
                    'imagen_url'=>'uploads/recetas/Risotto.jpg',
                    'ingredientes' => [
                        ['Arroz blanco', 300, 'g'],
                        ['Champiñones', 400, 'g'],
                        ['Cebolla', 1, 'unidad'],
                        ['Vino blanco', 100, 'ml'],
                        ['Caldo de verduras', 1, 'litro'],
                        ['Queso parmesano', 80, 'g'],
                        ['Mantequilla', 30, 'g']
                    ],
                    'instrucciones' => [
                        'Sofreír la cebolla y los champiñones.',
                        'Añadir el arroz y cocinar 2 minutos.',
                        'Incorporar el vino y dejar evaporar.',
                        'Añadir el caldo poco a poco removiendo.',
                        'Terminar con mantequilla y queso.'
                    ]
                ],

                [
                    'titulo' => 'Hamburguesas de lentejas',
                    'descripcion' => 'Hamburguesas vegetales de lentejas y especias',
                    'tiempo' => 35,
                    'dificultad' => 'media',
                    'categoria' => 'Vegetariano',
                    'imagen_url'=>'uploads/recetas/hamburguesadelentejas.jpeg',
                    'ingredientes' => [
                        ['Lentejas cocidas', 400, 'g'],
                        ['Cebolla', 1, 'unidad'],
                        ['Ajo', 2, 'dientes'],
                        ['Pan rallado', 100, 'g'],
                        ['Huevos', 2, 'unidades'],
                        ['Comino', 1, 'cucharadita'],
                        ['Aceite de oliva', 3, 'cucharadas']
                    ],
                    'instrucciones' => [
                        'Triturar las lentejas con cebolla y ajo.',
                        'Mezclar con huevo, pan rallado y especias.',
                        'Formar hamburguesas y refrigerar 15 minutos.',
                        'Cocinar a la plancha 5 minutos por cada lado.'
                    ]
                ],

                // Postres
                [
                    'titulo' => 'Brownie de chocolate',
                    'descripcion' => 'Brownie intenso de chocolate con nueces',
                    'tiempo' => 45,
                    'dificultad' => 'media',
                    'categoria' => 'Postres',
                    'imagen_url'=>'uploads/recetas/Browniedechocolate.jpg',
                    'ingredientes' => [
                        ['Chocolate negro', 200, 'g'],
                        ['Mantequilla', 150, 'g'],
                        ['Azúcar', 200, 'g'],
                        ['Huevos', 3, 'unidades'],
                        ['Harina de trigo', 100, 'g'],
                        ['Nueces', 100, 'g']
                    ],
                    'instrucciones' => [
                        'Derretir chocolate y mantequilla al baño maría.',
                        'Batir huevos con azúcar hasta espumar.',
                        'Incorporar chocolate y harina.',
                        'Añadir nueces y hornear 25 minutos.',
                        'Dejar enfriar antes de cortar.'
                    ]
                ],

                [
                    'titulo' => 'Tarta de queso',
                    'descripcion' => 'Tarta de queso cremosa al estilo New York',
                    'tiempo' => 60,
                    'dificultad' => 'media',
                    'categoria' => 'Postres',
                    'imagen_url'=>'uploads/recetas/Tarta de queso.jpg',
                     'imagen_url'=>'uploads/recetas/Tarta de queso.jpg',
                    'ingredientes' => [
                        ['Galletas digestive', 200, 'g'],
                        ['Mantequilla', 100, 'g'],
                        ['Queso crema', 500, 'g'],
                        ['Azúcar', 150, 'g'],
                        ['Huevos', 3, 'unidades'],
                        ['Nata líquida', 200, 'ml'],
                        ['Vainilla', 1, 'cucharadita']
                    ],
                    'instrucciones' => [
                        'Triturar galletas y mezclar con mantequilla.',
                        'Forrar el molde con la base de galleta.',
                        'Batir queso con azúcar y huevos.',
                        'Añadir nata y vainilla, verter sobre la base.',
                        'Hornear a 160°C durante 45 minutos.'
                    ]
                ],

                [
                    'titulo' => 'Flan de huevo',
                    'descripcion' => 'Flan tradicional de huevo con caramelo',
                    'tiempo' => 50,
                    'dificultad' => 'fácil',
                    'categoria' => 'Postres',
                    'imagen_url'=>'uploads/recetas/flandehuevo.jpg',
                    'ingredientes' => [
                        ['Huevos', 6, 'unidades'],
                        ['Leche', 500, 'ml'],
                        ['Azúcar', 150, 'g'],
                        ['Vainilla', 1, 'cucharadita']
                    ],
                    'instrucciones' => [
                        'Preparar caramelo y cubrir los moldes.',
                        'Batir huevos con azúcar y vainilla.',
                        'Añadir leche poco a poco sin batir en exceso.',
                        'Verter en moldes y cocinar al baño maría.',
                        'Refrigerar 4 horas antes de desmoldar.'
                    ]
                ],

                // Más recetas para completar las 20
                [
                    'titulo' => 'Gazpacho andaluz',
                    'descripcion' => 'Sopa fría de tomate y verduras',
                    'tiempo' => 20,
                    'dificultad' => 'fácil',
                    'categoria' => 'Sopas',
                    'imagen_url'=>'uploads/recetas/Gazpacho andaluz.jpeg',
                    'ingredientes' => [
                        ['Tomate', 1, 'kg'],
                        ['Pimiento verde', 1, 'unidad'],
                        ['Pepino', 1, 'unidad'],
                        ['Ajo', 1, 'diente'],
                        ['Aceite de oliva', 4, 'cucharadas'],
                        ['Vinagre', 2, 'cucharadas'],
                        ['Sal', 1, 'pizca']
                    ],
                    'instrucciones' => [
                        'Lavar y cortar todas las verduras.',
                        'Triturar todos los ingredientes.',
                        'Pasar por el colador para eliminar pieles.',
                        'Refrigerar 2 horas antes de servir.'
                    ]
                ],

                [
                    'titulo' => 'Tortilla de patatas',
                    'descripcion' => 'Tortilla española clásica con cebolla',
                    'tiempo' => 40,
                    'dificultad' => 'media',
                    'categoria' => 'Vegetariano',
                    'imagen_url'=>'uploads/recetas/tortilla.jpg',
                    'ingredientes' => [
                        ['Patata', 6, 'unidades'],
                        ['Huevos', 8, 'unidades'],
                        ['Cebolla', 1, 'unidad'],
                        ['Aceite de oliva', 1, 'vaso'],
                        ['Sal', 1, 'cucharadita']
                    ],
                    'instrucciones' => [
                        'Pelar y cortar patatas en rodajas finas.',
                        'Freír patatas y cebolla a fuego lento.',
                        'Batir los huevos y mezclar con las patatas.',
                        'Cuajar la tortilla por ambos lados.'
                    ]
                ],

                [
                    'titulo' => 'Pasta carbonara',
                    'descripcion' => 'Pasta con salsa cremosa de huevo y panceta',
                    'tiempo' => 25,
                    'dificultad' => 'media',
                    'categoria' => 'Carnes',
                    'imagen_url'=>'uploads/recetas/pastacarbonara.jpg',
                    'ingredientes' => [
                        ['Pasta', 400, 'g'],
                        ['Panceta', 200, 'g'],
                        ['Huevos', 3, 'unidades'],
                        ['Queso parmesano', 100, 'g'],
                        ['Pimienta negra', 1, 'cucharadita']
                    ],
                    'instrucciones' => [
                        'Cocinar la pasta al dente.',
                        'Dorar la panceta en una sartén.',
                        'Batir huevos con queso y pimienta.',
                        'Mezclar todo fuera del fuego.',
                        'Servir inmediatamente.'
                    ]
                ],

                [
                    'titulo' => 'Guacamole',
                    'descripcion' => 'Dip mexicano de aguacate y limón',
                    'tiempo' => 15,
                    'dificultad' => 'fácil',
                    'categoria' => 'Entrantes',
                    'imagen_url'=>'uploads/recetas/Guacamole.jpg',
                    'ingredientes' => [
                        ['Aguacate', 3, 'unidades'],
                        ['Limón', 1, 'unidad'],
                        ['Cebolla', 0.5, 'unidad'],
                        ['Tomate', 1, 'unidad'],
                        ['Cilantro', 2, 'cucharadas'],
                        ['Sal', 1, 'pizca']
                    ],
                    'instrucciones' => [
                        'Triturar los aguacates con un tenedor.',
                        'Picar finamente cebolla, tomate y cilantro.',
                        'Mezclar todo y añadir limón y sal.',
                        'Refrigerar 30 minutos antes de servir.'
                    ]
                ],

                [
                    'titulo' => 'Paella de marisco',
                    'descripcion' => 'Paella valenciana con marisco fresco',
                    'tiempo' => 60,
                    'dificultad' => 'alta',
                    'categoria' => 'Pescados',
                    'imagen_url' => 'uploads/recetas/paella.jpg',
                    'ingredientes' => [
                        ['Arroz blanco', 400, 'g'],
                        ['Gambas', 200, 'g'],
                        ['Mejillones', 500, 'g'],
                        ['Calamares', 300, 'g'],
                        ['Pimiento rojo', 1, 'unidad'],
                        ['Azafrán', 1, 'pizca'],
                        ['Caldo de pescado', 1, 'litro']
                    ],
                    'instrucciones' => [
                        'Sofreír las verduras en la paellera.',
                        'Añadir el marisco y cocinar 5 minutos.',
                        'Incorporar el arroz y el azafrán.',
                        'Añadir el caldo caliente y cocinar 18 minutos.',
                        'Dejar reposar 5 minutos antes de servir.'
                    ]
                ],

                [
                    'titulo' => 'Mousse de chocolate',
                    'descripcion' => 'Mousse ligera de chocolate negro',
                    'tiempo' => 30,
                    'dificultad' => 'media',
                    'categoria' => 'Postres',
                    'imagen_url'=>'uploads/recetas/Moussedechocolate.jpg',
                    'ingredientes' => [
                        ['Chocolate negro', 200, 'g'],
                        ['Huevos', 4, 'unidades'],
                        ['Azúcar', 50, 'g'],
                        ['Mantequilla', 50, 'g']
                    ],
                    'instrucciones' => [
                        'Derretir chocolate con mantequilla.',
                        'Separar yemas de claras.',
                        'Batir claras a punto de nieve con azúcar.',
                        'Mezclar yemas con chocolate templado.',
                        'Incorporar claras con movimientos suaves.',
                        'Refrigerar 4 horas antes de servir.'
                    ]
                ],

                [
                    'titulo' => 'Pollo curry',
                    'descripcion' => 'Pollo en salsa de curry y coco',
                    'tiempo' => 40,
                    'dificultad' => 'media',
                    'categoria' => 'Carnes',
                    'imagen_url'=>'uploads/recetas/Pollocurry.jpg',
                    'ingredientes' => [
                        ['Pechuga de pollo', 500, 'g'],
                        ['Cebolla', 1, 'unidad'],
                        ['Leche de coco', 400, 'ml'],
                        ['Curry en polvo', 2, 'cucharadas'],
                        ['Jengibre', 1, 'cucharadita'],
                        ['Aceite de oliva', 2, 'cucharadas']
                    ],
                    'instrucciones' => [
                        'Dorar el pollo cortado en dados.',
                        'Añadir cebolla y especias.',
                        'Incorporar leche de coco.',
                        'Cocinar 20 minutos a fuego lento.',
                        'Servir con arroz basmati.'
                    ]
                ],

                [
                    'titulo' => 'Ensalada de quinoa',
                    'descripcion' => 'Ensalada saludable de quinoa y verduras',
                    'tiempo' => 25,
                    'dificultad' => 'fácil',
                    'categoria' => 'Ensaladas',
                    'imagen_url'=>'uploads/recetas/ensaladadequinoa.jpg',
                    'ingredientes' => [
                        ['Quinoa', 200, 'g'],
                        ['Pepino', 1, 'unidad'],
                        ['Tomate', 2, 'unidades'],
                        ['Aguacate', 1, 'unidad'],
                        ['Limón', 1, 'unidad'],
                        ['Aceite de oliva', 3, 'cucharadas']
                    ],
                    'instrucciones' => [
                        'Cocinar quinoa según instrucciones.',
                        'Cortar todas las verduras en cubos.',
                        'Mezclar quinoa con verduras.',
                        'Aliñar con limón y aceite.',
                        'Servir fría o a temperatura ambiente.'
                    ]
                ]
            ];

            // Preparar statements para insertar
            $insertReceta = $this->conn->prepare("INSERT INTO recetas (titulo, descripcion, tiempo_preparacion, dificultad, categoria, imagen_url, usuario_id, votos_positivos, votos_negativos, destacada) VALUES (:titulo, :descripcion, :tiempo_preparacion, :dificultad, :categoria, :imagen_url, :usuario_id, :votos_positivos, :votos_negativos, :destacada) RETURNING id");
            $insertRecIng = $this->conn->prepare("INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad) VALUES (:receta_id, :ingrediente_id, :cantidad, :unidad)");
            $insertInstr = $this->conn->prepare("INSERT INTO receta_instrucciones (receta_id, orden, descripcion, imagen_url) VALUES (:receta_id, :orden, :descripcion, :imagen_url)");

            // Insertar cada receta
            foreach ($recetas as $receta) {
                $usuario_id = $userIds[array_rand($userIds)];
                $destacada = (rand(0, 4) === 0) ? 1 : 0;  // 20% de probabilidad de ser destacada

                // Insertar receta
                $insertReceta->execute([
                    ':titulo' => $receta['titulo'],
                    ':descripcion' => $receta['descripcion'],
                    ':tiempo_preparacion' => $receta['tiempo'],
                    ':dificultad' => $receta['dificultad'],
                    ':categoria' => $catIds[$receta['categoria']],
                    ':imagen_url' => $receta['imagen_url'],
                    ':usuario_id' => $usuario_id,
                    ':votos_positivos' => rand(5, 100),
                    ':votos_negativos' => rand(0, 15),
                    ':destacada' => $destacada
                ]);

                $recetaId = $insertReceta->fetchColumn();

                // Insertar ingredientes
                foreach ($receta['ingredientes'] as $ing) {
                    $ingredienteNombre = $ing[0];
                    if (isset($ingredienteIds[$ingredienteNombre])) {
                        $insertRecIng->execute([
                            ':receta_id' => $recetaId,
                            ':ingrediente_id' => $ingredienteIds[$ingredienteNombre],
                            ':cantidad' => $ing[1],
                            ':unidad' => $ing[2]
                        ]);
                    }
                }

                // Insertar instrucciones
                foreach ($receta['instrucciones'] as $orden => $descripcion) {
                    $insertInstr->execute([
                        ':receta_id' => $recetaId,
                        ':orden' => $orden + 1,
                        ':descripcion' => $descripcion,
                        ':imagen_url' => null
                    ]);
                }
            }

            // --- Comentarios realistas ---
            $insertComentario = $this->conn->prepare("INSERT INTO comentarios (receta_id, usuario_id, contenido, puntuacion) VALUES (:receta_id, :usuario_id, :contenido, :puntuacion)");

            // Obtener IDs de recetas
            $rstmt = $this->conn->query("SELECT id FROM recetas ORDER BY id");
            $rids = $rstmt->fetchAll(PDO::FETCH_COLUMN);

            // Comentarios organizados por tipo de receta
            $comentariosPostres = ["¡Increíblemente delicioso!", "Perfecto nivel de dulzor", "A mi familia le encantó", "Fácil de hacer y espectacular"];
            $comentariosSalados = ["Muy sabroso y bien equilibrado", "Perfecto para una cena especial", "Los ingredientes combinan genial", "Volveré a hacerlo seguro"];
            $comentariosFaciles = ["Sencillo y rápido de preparar", "Ideal para principiantes", "Resultado profesional con poco esfuerzo"];

            foreach ($rids as $rid) {
                $numComentarios = rand(1, 5);
                for ($c = 0; $c < $numComentarios; $c++) {
                    // Seleccionar comentario apropiado según el tipo de receta
                    $comentariosArray = $comentariosSalados;
                    $insertComentario->execute([
                        ':receta_id' => $rid,
                        ':usuario_id' => $userIds[array_rand($userIds)],
                        ':contenido' => $comentariosArray[array_rand($comentariosArray)],
                        ':puntuacion' => rand(4, 5) // Mayor probabilidad de puntuaciones altas
                    ]);
                }
            }

            // Confirmar transacción
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            echo json_encode(["error" => "Error inserting dummy data: " . $e->getMessage()]);
            return false;
        }
    }
}
