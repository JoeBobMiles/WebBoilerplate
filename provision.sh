#!/bin/bash
# provision.sh
# Unlike other source files in this repository, this file uses tabs instead of
# spaces since bash scripts are annoying and rely on tabs characters in order
# for certain formating tricks to work (looking at you '<<-').

# force exit on error so that we don't waste time.
set -e

# update the base box.
sudo apt-get update -y

# install nginx
sudo apt-get install nginx

# install mysql
sudo apt-get install -y mysql-server

# perform secure installation steps
mysql -uroot <<-MYSQL
	update mysql.user set password = password('root') where User = 'root';
	delete from mysql.user where User = '';
	delete from mysql.user where
		User = 'root' and
		Host not in ('localhost', '127.0.0.1', '::1');
	drop database test;
	delete from mysql.db where Db = 'test' or Db = 'test\_%';
	flush privileges;
MYSQL

# install php (7.0 for now, we'll deal with the remi repository later)
sudo apt-get install -y php-fpm php-mysql

# uncomment cgi.fix_path_info and set it to false

# change out nginx configuration

# setup symbolic link between /vagrant and /var/www/html (or whatever our
# target hosting directory is).