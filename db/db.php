<?php
$host = 'localhost'; // Cambia esto según tu configuración
$db = 'monitoring_system'; // Nombre de tu base de datos
$user = 'root'; // Tu usuario de la base de datos
$pass = ''; // Tu contraseña de la base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
    exit;
}
?>
