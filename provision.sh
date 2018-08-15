#!/bin/bash
#
# provision.sh

# force exit on error so that we don't waste time if something goes wrong.
set -e

apt-get update -y

MYSQL_PASSWORD='root'

debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD"
apt-get install -y nginx mysql-server

# installing php 7.0 for now, we'll deal with the remi repository later
sudo apt-get install -y php-fpm php-mysql

# setup php.ini the way we need/want it
sed -Ein 's/^;(cgi\.fix_pathinfo).*/\1=0/' /etc/php/7.0/fpm/php.ini
sed -Ein 's/^(error_reporting).*/\1=E_ALL/' /etc/php/7.0/fpm/php.ini
sed -Ein 's/^(display_errors).*/\1=On/' /etc/php/7.0/fpm/php.ini

# setup symbolic link between project nginx-config and actual nginx config.
rm -f /etc/nginx/sites-available/default
ln -s /vagrant/nginx-config /etc/nginx/sites-available/default
service nginx reload

# setup symbolic link between /vagrant and /var/www (or whatever our
# target hosting directory is).
rm -rf /var/www
ln -s /vagrant /var/www