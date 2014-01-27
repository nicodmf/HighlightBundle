#!/bin/bash

symfony_version=2.3.*

path_install=/var/www/code
path_symfony=/var/www/symfony

proxy=33.33.33.200

with_upgrade="non"

message=""

verbose="non"

first_files="\
/etc/resolv.conf \
/etc/hostname \
/etc/hosts \
/etc/apt/sources.list.d/dotdeb.list \
/etc/apt/apt.conf.d/01-proxy \
/etc/apt/detect-http-proxy.sh \
/etc/bash.bashrc \
"

lib_keys="http://www.dotdeb.org/dotdeb.gpg"

libs="git acl bash-completion php5 curl mysql-server phpmyadmin openssl ssl-cert vim rsync\
    php-apc php5-xdebug php5-intl php5-imap php5-mcrypt php5-curl less"
pecl_libs=""

apache_modules="rewrite ssl"

restart_services="apache2 ssh"

log="/vagrant/vagrant/provision.log"
error="/tmp/error"

#DB in format "base user password"
db="wdb wdb_user pass"
db_test="wdb_test wdb_test_user pass"
db_dev="wdb_dev wdb_dev_user pass"

composer=oui
github_oauth=5103b9b7ada3bebccb5c8d2a420a8ce6aa5bf1bb
acl=oui
symfony=non
symfony_install=oui
path_vagrant=/vagrant
web_user=vagrant
web_group=vagrant
path_composer=/usr/bin/composer

sslsubject="\
C=FR \
ST=IDF \
O=Cloud \
localityName=PARIS \
commonName=DOMAIN \
organizationalUnitName= \
emailAddress=admin@test.fr \
"
