<?php
session_start(); // Inicia la sesión

// Establecer la zona horaria
date_default_timezone_set('America/Guayaquil');

// Configuración de la base de datos
require_once('db/db.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Acceso prohibido
    echo "Acceso no autorizado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    $api_key = filter_input(INPUT_POST, 'api_key', FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id']; // Obtiene el ID del usuario de la sesión

    if ($action === 'add') {
        $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_IP);
        $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
        $contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);

        if (!$target || !$label || !$contact) {
            http_response_code(400); // Solicitud incorrecta
            echo "Datos inválidos.";
            exit();
        }

        // Agregar IP a HetrixTools
        $url = 'https://api.hetrixtools.com/v2/'.$api_key.'/blacklist/add/';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(["target" => $target, "label" => $label, "contact" => $contact]));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($curl);

        // Manejo de errores de cURL
        if ($result === false) {
            http_response_code(500); // Error del servidor
            echo "Error en la solicitud a HetrixTools: " . curl_error($curl);
            curl_close($curl);
            exit();
        }
        curl_close($curl);

        // Guardar en la base de datos
        $stmt = $pdo->prepare('INSERT INTO ip_entries (user_id, name, ip_address) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $label, $target]);

        echo $result;

    } elseif ($action === 'delete') {
        $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_IP);

        if (!$target) {
            http_response_code(400); // Solicitud incorrecta
            echo "Datos inválidos.";
            exit();
        }

        // Eliminar IP de HetrixTools
        $url = 'https://api.hetrixtools.com/v2/'.$api_key.'/blacklist/delete/';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(["target" => $target]));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($curl);

        // Manejo de errores de cURL
        if ($result === false) {
            http_response_code(500); // Error del servidor
            echo "Error en la solicitud a HetrixTools: " . curl_error($curl);
            curl_close($curl);
            exit();
        }
        curl_close($curl);

        // Eliminar de la base de datos
        $stmt = $pdo->prepare('DELETE FROM ip_entries WHERE ip_address = ? AND user_id = ?');
        $stmt->execute([$target, $user_id]);

        echo $result;

    } elseif ($action === 'update') {
        $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_IP);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $listed_in = filter_input(INPUT_POST, 'listed_in', FILTER_SANITIZE_STRING);

        if (!$target || !$status || !$listed_in) {
            http_response_code(400); // Solicitud incorrecta
            echo "Datos inválidos.";
            exit();
        }

        // Obtener la fecha y hora actual
        $current_datetime = date('Y-m-d H:i:s');

        // Actualizar en la base de datos
        $stmt = $pdo->prepare('UPDATE ip_entries SET status = ?, listed_in = ?, updated_at = ? WHERE ip_address = ? AND user_id = ?');
        $stmt->execute([$status, $listed_in, $current_datetime, $target, $user_id]);

        if ($stmt->errorCode() !== '00000') {
            error_log("SQL Error: " . implode(" ", $stmt->errorInfo()));
            http_response_code(500);
            echo "Error al actualizar en la base de datos.";
            exit();
        }

        echo "Datos actualizados correctamente.";

    } else {
        http_response_code(400); // Solicitud incorrecta
        echo "Acción no válida.";
    }
} else {
    http_response_code(405); // Método no permitido
    echo "Método no permitido.";
}




?>