#!/usr/bin/env bash

# Use single quotes instead of double quotes to make it work with special-character passwords
PASSWORD='password'
DBNAME='fias'

# update / upgrade
sudo apt-get update
sudo apt-get -y upgrade

# build essential
sudo apt-get install -y build-essential tcl

# install php 7
sudo apt-get install -y php7.0 php7.0-curl php7.0-xml php7.0-mcrypt php7.0-zip php7.0-dev php7.0-soap php7.0-sqlite3 php7.0-mbstring

# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password ${PASSWORD}"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password ${PASSWORD}"
sudo apt-get install -y mysql-server
sudo apt-get install -y php7.0-mysql

# create database
mysql -uroot -p"${PASSWORD}" -e "create database if not exists ${DBNAME} DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;"

# install php rar extension
sudo pecl -v install rar
echo "extension=rar.so" >> /etc/php/7.0/cli/php.ini

# install Composer
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
