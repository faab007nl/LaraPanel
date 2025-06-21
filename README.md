# LaraPanel

About


## Install

Install Script will come later

```bash
#!/bin/bash

# Check if script is run as root
if [[ $EUID -ne 0 ]]; then
    echo "This script must be run as root. Use sudo."
    exit 1
fi

# Update and upgrade the system
echo "Updating and upgrading the system..."
apt-get update || { echo "Failed to update package lists"; exit 1; }
apt-get upgrade -y || { echo "Failed to upgrade system"; exit 1; }
apt-get dist-upgrade -y || { echo "Failed to perform dist-upgrade"; exit 1; }

# Install nginx
echo "Installing nginx..."
apt-get install -y nginx || { echo "Failed to install nginx"; exit 1; }

# Add PHP repository (for multiple PHP versions)
echo "Adding PHP repository..."
apt-get install -y software-properties-common || { echo "Failed to install software-properties-common"; exit 1; }
add-apt-repository -y ppa:ondrej/php || { echo "Failed to add PHP repository"; exit 1; }
apt-get update || { echo "Failed to update package lists after adding PHP repository"; exit 1; }

# Install PHP versions and common extensions
PHP_VERSIONS=("8.1" "8.2" "8.3" "8.4")
PHP_EXTENSIONS=("fpm" "cli" "common" "mysql" "curl" "gd" "mbstring" "xml" "zip" "bcmath" "json" "tokenizer")

for version in "${PHP_VERSIONS[@]}"; do
    echo "Installing PHP $version..."
    for ext in "${PHP_EXTENSIONS[@]}"; do
        apt-get install -y "php${version}-${ext}" || { echo "Failed to install php${version}-${ext}"; exit 1; }
    done
done

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
for version in "${PHP_VERSIONS[@]}"; do
    systemctl start "php${version}-fpm" || { echo "Failed to start php${version}-fpm"; exit 1; }
    systemctl enable "php${version}-fpm" || { echo "Failed to enable php${version}-fpm"; exit 1; }
done

# Create sudo user larapanel
echo "Creating sudo user larapanel..."
useradd -m -s /bin/bash larapanel || { echo "Failed to create user larapanel"; exit 1; }
echo "larapanel:larapanel" | chpasswd || { echo "Failed to set password for larapanel"; exit 1; }

# Add larapanel to sudoers with NOPASSWD
echo "Configuring sudoers for larapanel..."
echo "larapanel ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers || { echo "Failed to configure sudoers"; exit 1; }

# Verify sudoers configuration
visudo -c || { echo "sudoers file syntax check failed"; exit 1; }

# Configure SSH to allow larapanel only from localhost
echo "Configuring SSH to restrict larapanel to localhost connections..."
echo "Match User larapanel" >> /etc/ssh/sshd_config || { echo "Failed to append to sshd_config"; exit 1; }
echo "    AllowUsers larapanel@localhost" >> /etc/ssh/sshd_config || { echo "Failed to append AllowUsers to sshd_config"; exit 1; }

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
for version in "${PHP_VERSIONS[@]}"; do
    php${version} -v
done
mysql --version
id larapanel
sshd -v
mysql -e "SHOW DATABASES LIKE '${MYSQL_DB}';" | grep "${MYSQL_DB}" || { echo "Database ${MYSQL_DB} not found"; exit 1; }

echo "Installation and configuration completed successfully!"
echo "System upgraded, nginx, PHP 8.1, 8.2, 8.3, 8.4, MariaDB, and OpenSSH installed and running."
echo "User larapanel created with password 'larapanel', passwordless sudo privileges, and SSH restricted to localhost."
echo "MySQL user 'larapanel' created with a random password and database 'larapanel'."
echo "MySQL credentials saved to /root/larapanel_mysql_credentials.txt"
```
