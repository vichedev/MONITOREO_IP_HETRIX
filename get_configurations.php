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

// Obtener las configuraciones del usuario
$stmt = $pdo->prepare('SELECT id, name, api_token, contact_list_id FROM api_settings WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$configurations = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode($configurations);
?>