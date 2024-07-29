<?php
session_start(); // Inicia la sesión

// Establecer la zona horaria
date_default_timezone_set('America/Guayaquil');

// Configuración de la base de datos
$host = 'localhost';
$db = 'monitoring_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Acceso prohibido
    echo "Acceso no autorizado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_SESSION['user_id']; // Obtiene el ID del usuario de la sesión

    // Obtener el API token y el ID de Lista de contacto del usuario
    $stmt = $pdo->prepare('SELECT api_token, contact_list_id FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'apiToken' => $user['api_token'],
            'contactListId' => $user['contact_list_id']
        ]);
    } else {
        http_response_code(404); // No encontrado
        echo "Datos no encontrados.";
    }
}
?>
