<?php
// Establece la zona horaria manualmente para este script
date_default_timezone_set('America/Guayaquil');

// Imprime la zona horaria actual
echo 'La zona horaria actual es: ' . date_default_timezone_get() . '<br>';

// Imprime la fecha y hora actual
echo 'La fecha y hora actual es: ' . date('Y-m-d H:i:s');
?>
