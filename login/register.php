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
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validar datos
    if (empty($username) || empty($password) || empty($email)) {
        echo "Por favor, complete todos los campos requeridos.";
        exit();
    }

    // Verificar si el nombre de usuario ya está en uso
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "El nombre de usuario ya está en uso.";
        exit();
    }

    // Verificar si el email ya está en uso
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "El email ya está registrado.";
        exit();
    }

    try {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (:username, :password_hash, :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Iniciar sesión para el usuario recién registrado
        $_SESSION['user'] = $username;
        header("Location: ../index.php"); // Redirige a index.php
        exit();
    } catch (PDOException $e) {
        echo "Error al registrar el usuario: " . $e->getMessage();
    }
}
?>