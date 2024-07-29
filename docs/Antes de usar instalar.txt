#!/bin/bash

# Actualizar el sistema
sudo apt-get update

# Instalar Apache y PHP con cURL
sudo apt-get install -y apache2 php php-curl

# Habilitar mod_rewrite para Apache
sudo a2enmod rewrite

# Reiniciar Apache para aplicar cambios
sudo systemctl restart apache2

# Cambiar permisos para que Apache pueda acceder a los archivos
sudo chown -R www-data:www-data /var/www/html

# Obtener la dirección IP del servidor
SERVER_IP=$(hostname -I | awk '{print $1}')

# Mensaje final
echo "Instalador completado. Accede desde: http://$SERVER_IP/"
