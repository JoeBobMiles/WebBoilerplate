#!/bin/bash
#
# provision.sh
#
# Unlike other source files in this repository, this file uses tabs instead of
# spaces. The reason is because bash scripts rely on tabs characters for
# certain formating tricks to work (looking at you '<<-').

# force exit on error so that we don't waste time if something goes wrong.
set -e

apt-get update -y

MYSQL_PASSWORD='root'

debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD"
apt-get install -y nginx mysql-server

# installing php 7.0 for now, we'll deal with the remi repository later
sudo apt-get install -y php-fpm php-mysql

# uncomment cgi.fix_path_info and set it to false
sed -Ein 's/^;(cgi\.fix_pathinfo).*/\1=0/' /etc/php/7.0/fpm/php.ini

# change out nginx configuration and link.
rm -f /etc/nginx/sites-available/default
ln -s /vagrant/nginx-config /etc/nginx/sites-available/default
service nginx reload

# setup symbolic link between /vagrant and /var/www (or whatever our
# target hosting directory is).
rm -rf /var/www
ln -s /vagrant /var/www