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

// Configuración de la base de datos
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

// Obtener las configuraciones del usuario
$stmt = $pdo->prepare('SELECT id, name, api_token, contact_list_id FROM api_settings WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$configurations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verificación de Listas Negras</title>
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
            <!-- Tarjeta para Agregar Configuración -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Agregar Configuración</h5>
                    </div>
                    <div class="card-body">
                        <form id="add-config-form">
                            <div class="mb-3">
                                <label for="config-name" class="form-label">Nombre de Configuración</label>
                                <input type="text" id="config-name" class="form-control"
                                    placeholder="Nombre para la configuración" required />
                            </div>
                            <div class="mb-3">
                                <label for="api-token-new" class="form-label">Token API</label>
                                <input type="text" id="api-token-new" class="form-control"
                                    placeholder="Ingrese su Token API" required />
                            </div>
                            <div class="mb-3">
                                <label for="contact-list-id-new" class="form-label">ID Lista de contacto</label>
                                <input type="text" id="contact-list-id-new" class="form-control"
                                    placeholder="Ingrese el ID de Lista de contacto" required />
                            </div>
                            <button type="button" class="btn btn-primary" onclick="saveConfiguration()">Guardar
                                Configuración</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Configuración de HetrixTools -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Configuración de HetrixTools</h5>
                    </div>
                    <div class="card-body">
                        <!-- Selección de Configuración -->
                        <div class="mb-3">
                            <label for="config-select" class="form-label">Seleccionar Configuración</label>
                            <select id="config-select" class="form-select" onchange="loadConfiguration(this.value)">
                                <option value="">Seleccionar configuración</option>
                                <?php foreach ($configurations as $config): ?>
                                <option value="<?php echo htmlspecialchars($config['id']); ?>">
                                    <?php echo htmlspecialchars($config['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Campos para Token API y ID de Lista -->
                        <div class="mb-3">
                            <label for="api-token" class="form-label">Token API</label>
                            <div class="input-group">
                                <input type="password" id="api-token" class="form-control"
                                    placeholder="Ingrese su Token API" disabled />
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="toggleVisibility('api-token')">👁️</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contact-list-id" class="form-label">ID Lista de contacto</label>
                            <div class="input-group">
                                <input type="password" id="contact-list-id" class="form-control"
                                    placeholder="Ingrese el ID de Lista de contacto" disabled />
                                <div class="input-group-append">
                                    <span class="input-group-text"
                                        onclick="toggleVisibility('contact-list-id')">👁️</span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="validateApiToken()">Validar Credenciales</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Agregar IP -->
        <div class="card mt-4">
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
    <script>
    // Boton de guardar
    function saveConfiguration() {
        const name = document.getElementById('config-name').value;
        const apiToken = document.getElementById('api-token-new').value;
        const contactListId = document.getElementById('contact-list-id-new').value;

        if (!name || !apiToken || !contactListId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, complete todos los campos.',
            });
            return;
        }

        fetch('add_config.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'name': name,
                    'api_token': apiToken,
                    'contact_list_id': contactListId
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Éxito' : 'Error',
                    text: data.message,
                }).then(() => {
                    if (data.success) {
                        // Recargar el selector de configuraciones
                        loadConfigurations();

                        // Limpiar los campos del formulario
                        document.getElementById('add-config-form').reset();
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al guardar la configuración.',
                });
            });
    }


    // Cargar configuraciones en el desplegable
    function loadConfigurations() {
        fetch('get_configurations.php')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('config-select');
                select.innerHTML = '<option value="">Seleccionar configuración</option>';
                data.forEach(config => {
                    select.innerHTML += `<option value="${config.id}">${config.name}</option>`;
                });
            })
            .catch(error => console.error('Error:', error));
    }
    </script>
</body>

</html>