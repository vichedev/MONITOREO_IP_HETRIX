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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-danger" onclick="logout()">Cerrar Sesión</button>
        </div>
        <h2 class="mb-4">Configuración de HetrixTools</h2>
        <div class="mb-3">
            <label for="username" class="form-label">Usuario:</label>
            <input type="text" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled />
        </div>
        <div class="mb-3">
            <label for="api-token" class="form-label">Token API</label>
            <input type="text" id="api-token" class="form-control" placeholder="Ingrese su Token API" />
        </div>
        <div class="mb-3">
            <label for="contact-list-id" class="form-label">ID Lista de contacto</label>
            <input type="text" id="contact-list-id" class="form-control" placeholder="Ingrese el ID de Lista de contacto" />
        </div>
        <button class="btn btn-primary" onclick="validateApiToken()">Validar Credenciales</button>
        <div id="message" class="alert mt-3" role="alert"></div>
    </div>

    <div class="container mt-4">
        <h2 class="mb-4">Monitor IPs: Lista Negra</h2>
        <div class="mb-3">
            <label for="new-name" class="form-label">Nombre</label>
            <input type="text" id="new-name" class="form-control" placeholder="Servidor Correo" />
        </div>
        <div class="mb-3">
            <label for="new-ip" class="form-label">IP V4</label>
            <input type="text" id="new-ip" class="form-control" placeholder="8.8.8.8" />
        </div>
        <button class="btn btn-success" onclick="registerRow()">Agregar IP</button>

        <table  id="ip-table" class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>IP</th>
                    <th>Estado</th>
                    <th>Listado en</th>
                    <th>Actualizado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Inicialmente vacío -->
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="assets/js/hetrix.js"></script>
</body>

</html>
