<?php
session_start(); // Inicia la sesión

require_once('db/db.php');
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id']; // Obtiene el ID del usuario de la sesión

$stmt = $pdo->prepare('SELECT name, ip_address FROM ip_entries WHERE user_id = ?');
$stmt->execute([$user_id]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($entries);
?>
