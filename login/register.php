<?php
session_start(); // Inicia la sesión

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $apiToken = $_POST['api_token'] ?? null; // Obtiene el token API, puede ser null si no se proporciona
    $contactListId = $_POST['contact_list_id'] ?? null; // Obtiene el ID de lista de contacto, puede ser null si no se proporciona

    // Verificar si el nombre de usuario ya está en uso
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "El nombre de usuario ya está en uso.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, api_token, contact_list_id) VALUES (:username, :password_hash, :email, :api_token, :contact_list_id)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_token', $apiToken);
        $stmt->bindParam(':contact_list_id', $contactListId);
        $stmt->execute();

        // Iniciar sesión para el usuario recién registrado
        $_SESSION['user'] = $username;
        header("Location: ../index.php"); // Redirige a index.php
        exit();
    }
}
?>
