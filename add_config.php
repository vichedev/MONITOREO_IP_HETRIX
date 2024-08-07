<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

// Configuración de la base de datos
$host = 'localhost';
$db = 'monitoring_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Obtén los datos del formulario
$name = $_POST['name'] ?? '';
$apiToken = $_POST['api_token'] ?? '';
$contactListId = $_POST['contact_list_id'] ?? '';

// Validación de datos
if (empty($name) || empty($apiToken) || empty($contactListId)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit();
}

// Actualiza o inserta los datos en la base de datos
try {
    // Verifica si ya existe una configuración para el usuario y el nombre proporcionado
    $stmt = $pdo->prepare('SELECT id FROM api_settings WHERE user_id = ? AND name = ?');
    $stmt->execute([$_SESSION['user_id'], $name]);
    $existingConfig = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingConfig) {
        // Actualiza la configuración existente
        $stmt = $pdo->prepare('UPDATE api_settings SET api_token = ?, contact_list_id = ? WHERE user_id = ? AND name = ?');
        $result = $stmt->execute([$apiToken, $contactListId, $_SESSION['user_id'], $name]);
        $message = 'Configuración actualizada correctamente.';
    } else {
        // Inserta una nueva configuración
        $stmt = $pdo->prepare('INSERT INTO api_settings (user_id, name, api_token, contact_list_id) VALUES (?, ?, ?, ?)');
        $result = $stmt->execute([$_SESSION['user_id'], $name, $apiToken, $contactListId]);
        $message = 'Configuración agregada correctamente.';
    }

    if ($result) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la configuración.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $e->getMessage()]);
}
?>
