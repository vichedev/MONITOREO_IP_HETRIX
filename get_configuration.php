<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.html');
    exit();
}

// Configuración de la base de datos
require_once('db/db.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Obtén el ID de la configuración
$configId = $_GET['id'] ?? '';

if (empty($configId)) {
    echo json_encode(null);
    exit();
}

// Obtener la configuración
$stmt = $pdo->prepare('SELECT api_token, contact_list_id FROM api_settings WHERE user_id = ? AND id = ?');
$stmt->execute([$_SESSION['user_id'], $configId]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($config);
?>