<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    // Redirige al usuario a la página de login
    header('Location: login/login.html');
    exit(); // Asegúrate de detener la ejecución del script después de redirigir
}
// Código para mostrar el nombre del usuario
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'No disponible';

// Código de `index.php` sigue aquí...
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verificación de Listas Negras - HetrixTools</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <button onclick="logout()" style="align-self: flex-end;">Cerrar Sesión</button>
        <h2>Configuración de HetrixTools</h2>
        <div class="input-group">
            <label for="username">Usuario:</label>
            <input type="text" id="username" value="<?php echo htmlspecialchars($username); ?>" disabled />
        </div>
        <div class="input-group">
            <label for="api-token">Token API</label>
            <input type="text" id="api-token" placeholder="Ingrese su Token API" />
        </div>
        <div class="input-group">
            <label for="contact-list-id">ID Lista de contacto</label>
            <input type="text" id="contact-list-id" placeholder="Ingrese el ID de Lista de contacto" />
        </div>
        <button onclick="validateApiToken()">Validar Credenciales</button>
        <div id="message" class="message"></div>
    </div>

    <div class="table-container">
        <h2>Monitor IPs: Lista Negra</h2>
        <div class="input-group">
            <label for="new-name">Nombre</label>
            <input type="text" id="new-name" placeholder="Servidor Correo" />
        </div>
        <div class="input-group">
            <label for="new-ip">IP V4</label>
            <input type="text" id="new-ip" placeholder="8.8.8.8" />
        </div>
        <button onclick="registerRow()">Agregar IP</button>

        <table id="ip-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>IP</th>
                    <th>ESTADO</th>
                    <th>LISTADO EN</th>
                    <th>ACTUALIZADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <!-- Inicialmente vacío -->
            </tbody>
        </table>
    </div>
    <script src="assets/js/app.js"></script>>
</body>

</html>