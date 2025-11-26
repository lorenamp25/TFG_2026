<?php


header('Content-Type: application/json');

require_once 'Database.php';
require_once 'Usuario.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
        exit;
    }

    // Leer JSON del body (desde Angular)
    $input = json_decode(file_get_contents('php://input'), true);

    $nickname = $input['nickname'] ?? null;
    $nombre = $input['nombre'] ?? null;
    $apellido = $input['apellido'] ?? null;
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    $fecha_nacimiento = $input['fecha_nacimiento'] ?? null;

    // Validaciones básicas
    if (!$nickname || !$email || !$password) {
        http_response_code(400);
        echo json_encode(["error" => "Nickname, email y contraseña son obligatorios"]);
        exit;
    }

    $usuario = new Usuario($conn);

    // Comprobar si ya existe el email
    $existe = $usuario->getByMail($email);
    if ($existe) {
        http_response_code(400);
        echo json_encode(["error" => "El email ya está registrado"]);
        exit;
    }

    // Asignar propiedades al modelo
    $usuario->nickname = $nickname;
    $usuario->nombre = $nombre;
    $usuario->apellido = $apellido;
    $usuario->email = $email;

    // 👉 AQUÍ encriptamos la contraseña (sin tocar Usuario.php)
    $usuario->password = password_hash($password, PASSWORD_DEFAULT);

    $usuario->fecha_nacimiento = $fecha_nacimiento;
    $usuario->puntuacion = 0;
    $usuario->es_admin = false;

    // Crear usuario en la BD
    $newId = $usuario->create();

    // Devolver usuario creado (sin password)
    echo json_encode([
        "ok" => true,
        "usuario" => [
            "id" => $newId,
            "nickname" => $usuario->nickname,
            "nombre" => $usuario->nombre,
            "apellido" => $usuario->apellido,
            "email" => $usuario->email,
            "fecha_nacimiento" => $usuario->fecha_nacimiento,
            "puntuacion" => $usuario->puntuacion,
            "es_admin" => $usuario->es_admin
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
}
