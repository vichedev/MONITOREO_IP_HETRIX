<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está logueado
if (isset($_SESSION['user_id'])) {
    // Aquí debes obtener el nombre del usuario de la base de datos o de la sesión
    // Suponiendo que el nombre del usuario está almacenado en $_SESSION['username']
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Desconocido';

    // Devolver el nombre de usuario en formato JSON
    echo json_encode(['username' => $username]);
} else {
    // Si no está logueado, devolver un error o mensaje adecuado
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
}
?>
