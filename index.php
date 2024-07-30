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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verificación de Listas Negras - HetrixTools</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <link rel="stylesheet" href="assets/css/app.css">
</head>

<body>
    <div class="container mt-4">
        <!-- Barra de Navegación -->
        <nav class="navbar navbar-light bg-light mb-4">
            <a class="navbar-brand" href="#">MONITOREO IP <span class="user-greeting">Bienvenido,
                    <?php echo htmlspecialchars($username); ?></span></a>
            <div class="d-flex">
                <button class="btn btn-danger" onclick="logout()">Cerrar Sesión</button>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <div class="row">
            <!-- Tarjeta de Configuración HetrixTools -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Configuración de HetrixTools</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="api-token" class="form-label">Token API</label>
                            <input type="text" id="api-token" class="form-control" placeholder="Ingrese su Token API"
                                disabled />
                        </div>
                        <div class="mb-3">
                            <label for="contact-list-id" class="form-label">ID Lista de contacto</label>
                            <input type="text" id="contact-list-id" class="form-control"
                                placeholder="Ingrese el ID de Lista de contacto" disabled />
                        </div>
                        <button class="btn btn-primary" onclick="validateApiToken()">Validar Credenciales</button>
                        <span id="message"></span>

                    </div>
                </div>
            </div>

            <!-- Tarjeta para Agregar IP -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Agregar IP</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="new-name" class="form-label">Nombre</label>
                            <input type="text" id="new-name" class="form-control" placeholder="Servidor Correo" />
                        </div>
                        <div class="mb-3">
                            <label for="new-ip" class="form-label">IP V4</label>
                            <input type="text" id="new-ip" class="form-control" placeholder="8.8.8.8" />
                        </div>
                        <button class="btn btn-success" onclick="registerRow()">Agregar IP</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabla de IPs -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Lista Negra de IPs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ip-table" class="table table-striped table-bordered">
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/hetrix.js"></script>
</body>

</html>