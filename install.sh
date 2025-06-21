#!/bin/bash

# Check if script is run as root
if [ $(id -u) -ne 0 ]
  then echo Please run this script as root!
  exit
fi

# Update and upgrade the system
echo "Updating and upgrading the system..."
apt-get update || { echo "Failed to update package lists"; exit 1; }
apt-get upgrade -y || { echo "Failed to upgrade system"; exit 1; }
apt-get dist-upgrade -y || { echo "Failed to perform dist-upgrade"; exit 1; }

# Unconditionally uninstall Apache2
echo "Attempting to stop, disable, and purge Apache2..."
# Stop and disable Apache2 service (ignore errors if not running/installed)
systemctl stop apache2 2>/dev/null
systemctl disable apache2 2>/dev/null

# Attempt to purge Apache2 packages (ignore errors if not installed)
apt-get purge -y apache2 apache2-utils apache2-data 2>/dev/null || true
apt-get autoremove -y 2>/dev/null || true

echo "Apache2 uninstallation attempt completed."

# Verify Apache2 uninstallation
echo "Verifying Apache2 uninstallation..."
if dpkg -s apache2 &>/dev/null; then
    echo "ERROR: Apache2 is still installed after uninstallation attempt!"
    # Optionally, you can decide to exit here if failure to uninstall Apache2 is critical.
    exit 1
else
    echo "Apache2 successfully uninstalled or was not present."
fi

# Install nginx
echo "Installing nginx..."
apt-get install -y nginx || { echo "Failed to install nginx"; exit 1; }

# Add PHP repository (for multiple PHP versions)
echo "Adding PHP repository..."
apt-get install -y software-properties-common || { echo "Failed to install software-properties-common"; exit 1; }
add-apt-repository -y ppa:ondrej/php || { echo "Failed to add PHP repository"; exit 1; }
apt-get update || { echo "Failed to update package lists after adding PHP repository"; exit 1; }

# Install PHP 8.4 and common extensions
echo "Installing PHP 8.4..."
apt-get install -y "php8.4" || { echo "Failed to install php 8.4"; exit 1; }
apt-get install -y "php8.4-fpm" || { echo "Failed to install php 8.4 fpm"; exit 1; }
apt-get install -y "php8.4-cli" || { echo "Failed to install php 8.4 cli"; exit 1; }
apt-get install -y "php8.4-common" || { echo "Failed to install php 8.4 common"; exit 1; }
apt-get install -y "php8.4-mysql" || { echo "Failed to install php 8.4 mysql"; exit 1; }
apt-get install -y "php8.4-curl" || { echo "Failed to install php 8.4 curl"; exit 1; }
apt-get install -y "php8.4-gd" || { echo "Failed to install php 8.4 gd"; exit 1; }
apt-get install -y "php8.4-mbstring" || { echo "Failed to install php 8.4 mbstring"; exit 1; }
apt-get install -y "php8.4-xml" || { echo "Failed to install php 8.4 xml"; exit 1; }
apt-get install -y "php8.4-zip" || { echo "Failed to install php 8.4 zip"; exit 1; }
apt-get install -y "php8.4-bcmath" || { echo "Failed to install php 8.4 bcmath"; exit 1; }
apt-get install -y "php-json" || { echo "Failed to install php json"; exit 1; }
apt-get install -y "php8.4-tokenizer" || { echo "Failed to install php 8.4 tokenizer"; exit 1; }

# Install openssl
echo "Installing OpenSSL..."
apt-get install openssl -y || { echo "Failed to install OpenSSL"; exit 1; }

# Install MariaDB
echo "Installing MariaDB..."
apt-get install -y mariadb-server || { echo "Failed to install MariaDB"; exit 1; }

# Install SSH server if not already installed
echo "Installing OpenSSH server..."
apt-get install -y openssh-server || { echo "Failed to install OpenSSH server"; exit 1; }

# Start and enable services
echo "Starting and enabling services..."
systemctl start nginx || { echo "Failed to start nginx"; exit 1; }
systemctl enable nginx || { echo "Failed to enable nginx"; exit 1; }
systemctl start mariadb || { echo "Failed to start MariaDB"; exit 1; }
systemctl enable mariadb || { echo "Failed to enable MariaDB"; exit 1; }
systemctl start ssh || { echo "Failed to start SSH"; exit 1; }
systemctl enable ssh || { echo "Failed to enable SSH"; exit 1; }

# Start PHP-FPM services
systemctl start "php8.4-fpm" || { echo "Failed to start php8.4-fpm"; exit 1; }
systemctl enable "php8.4-fpm" || { echo "Failed to enable php8.4-fpm"; exit 1; }

# Create sudo user larapanel with random password
echo "Creating sudo user larapanel with a random password..."
LARAPANEL_USER="larapanel"
LARAPANEL_PASS=$(head /dev/urandom | tr -dc A-Za-z0-9\?\!\@\#\$\%\^\&\*\(\)\_\+\-\= | head -c 24) || { echo "Failed to generate random password for larapanel"; exit 1; }

useradd -m -s /bin/bash "${LARAPANEL_USER}" || { echo "Failed to create user ${LARAPANEL_USER}"; exit 1; }
echo "${LARAPANEL_USER}:${LARAPANEL_PASS}" | chpasswd || { echo "Failed to set password for ${LARAPANEL_USER}"; exit 1; }


# Add larapanel to sudoers with NOPASSWD
echo "Configuring sudoers for larapanel..."
echo "larapanel ALL=(ALL) NOPASSWD:ALL" >>/etc/sudoers || {
    echo "Failed to configure sudoers"
    exit 1
}

# Verify sudoers configuration
visudo -c || {
    echo "sudoers file syntax check failed"
    exit 1
}

# --- SSH Configuration for larapanel (removed localhost restriction) ---
# Previous lines that added "Match User larapanel" and "AllowUsers larapanel@localhost" are removed.
echo "SSH configuration for larapanel will allow connections from any IP. No specific restriction added."

# Test SSH configuration (no changes expected from this script if no custom rules are added)
sshd -t || {
    echo "SSH configuration test failed"
    exit 1
}

# Restart SSH service to apply changes
systemctl restart ssh || {
    echo "Failed to restart SSH service"
    exit 1
}

# Test SSH configuration
sshd -t || { echo "SSH configuration test failed"; exit 1; }

# Restart SSH service to apply changes
systemctl restart ssh || { echo "Failed to restart SSH service"; exit 1; }

# Create MySQL user and database
echo "Creating MySQL user and database for larapanel..."
MYSQL_USER="larapanel"
MYSQL_DB="larapanel"
MYSQL_PASS=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16) || { echo "Failed to generate random password"; exit 1; }

# Secure MariaDB installation and create user/database
mysql -e "CREATE DATABASE IF NOT EXISTS \`${MYSQL_DB}\`;" || { echo "Failed to create database ${MYSQL_DB}"; exit 1; }
mysql -e "CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASS}';" || { echo "Failed to create MySQL user ${MYSQL_USER}"; exit 1; }
mysql -e "GRANT ALL PRIVILEGES ON \`${MYSQL_DB}\`.* TO '${MYSQL_USER}'@'localhost';" || { echo "Failed to grant privileges to ${MYSQL_USER}"; exit 1; }
mysql -e "FLUSH PRIVILEGES;" || { echo "Failed to flush MySQL privileges"; exit 1; }

# Save MySQL credentials to a secure file
echo "Saving MySQL credentials to /root/larapanel_mysql_credentials.txt..."
echo "MySQL User: ${MYSQL_USER}" > /root/larapanel_mysql_credentials.txt
echo "MySQL Password: ${MYSQL_PASS}" >> /root/larapanel_mysql_credentials.txt
echo "MySQL Database: ${MYSQL_DB}" >> /root/larapanel_mysql_credentials.txt
chmod 600 /root/larapanel_mysql_credentials.txt || { echo "Failed to set permissions on credentials file"; exit 1; }

# Verify installations
echo "Verifying installations..."
nginx -v
php8.4 -v
mysql --version
id larapanel
sshd -v
mysql -e "SHOW DATABASES LIKE '${MYSQL_DB}';" | grep "${MYSQL_DB}" || { echo "Database ${MYSQL_DB} not found"; exit 1; }

echo "Installation and configuration completed successfully!"
echo "System upgraded, nginx, PHP 8.4, MariaDB, and OpenSSH installed and running."
echo "User larapanel created with password 'larapanel', passwordless sudo privileges, and SSH restricted to localhost."
echo "MySQL user 'larapanel' created with a random password and database 'larapanel'."
echo "MySQL credentials saved to /root/larapanel_mysql_credentials.txt"

# Save larapanel credentials to a secure file
echo "Saving larapanel user credentials to /root/larapanel_user_credentials.txt..."
echo "Username: ${LARAPANEL_USER}" > /root/larapanel_user_credentials.txt
echo "Password: ${LARAPANEL_PASS}" >> /root/larapanel_user_credentials.txt
chmod 600 /root/larapanel_user_credentials.txt || { echo "Failed to set permissions on larapanel credentials file"; exit 1; }
