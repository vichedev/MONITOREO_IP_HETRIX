<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

// Configuración de la base de datos
require_once('db/db.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Obtén el ID de la configuración desde la solicitud
$configId = $_GET['id'] ?? '';

if (empty($configId)) {
    echo json_encode(['success' => false, 'message' => 'ID de configuración no proporcionado.']);
    exit();
}

try {
    // Consulta la configuración correspondiente
    $stmt = $pdo->prepare('SELECT api_token, contact_list_id FROM api_settings WHERE id = ? AND user_id = ?');
    $stmt->execute([$configId, $_SESSION['user_id']]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($config) {
        echo json_encode(['success' => true, 'api_token' => $config['api_token'], 'contact_list_id' => $config['contact_list_id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Configuración no encontrada.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $e->getMessage()]);
}
?>