#!/bin/bash

cd /vagrant/vagrant/bin
source _config.sh
source utils.sh

echo "" > $log

#define proxy
if [[ "$proxy" != "" ]]
then
    if [[ "$(ping -c 1 -w 5 $proxy | grep -o '. received'|cut -d' ' -f1)" = "1" ]]
    then
        export http_proxy=http://$proxy:3128/
        export https_proxy=$http_proxy
        #export ftp_proxy=$http_proxy
        #export rsync_proxy=$http_proxy
        export no_proxy="localhost,127.0.0.1,localaddress,.localdomain.com"
     fi
fi


echo "Install"

shexec prepare_link_system_files
shexec copy_init_system_files $first_files

shexec hostname $(cat /etc/hostname)
[[ "$lib_keys" != "" ]] && shexec add_lib_keys $lib_keys
shexec install_libs $libs
[[ "$pecl_libs" != "" ]] && shexec install_pecl_libs $pecl_libs
shexec copy_sys_files
shexec replace_in_file /etc/bash.bashrc __proxy__ $proxy
shexec reset_mysql_password

#Copie des fichiers

#Crée une base de donnée et l'utilisateur test
[[ "$db" != "" ]] && shexec create_user_e_db $db
#Crée une base de donnée et l'utilisateur
[[ "$db_test" != "" ]] && shexec create_user_e_db $db_test
#Crée une base de donnée et l'utilisateur
[[ "$db_dev" != "" ]] && shexec create_user_e_db $db_dev
#Ajouter le certificat ssl pour apache
[[ "$sslsubject" != "" ]] && shexec installSSL $sslsubject
#Ajouter les modules apache
[[ "$apache_modules" != "" ]] && shexec a2enmod $apache_modules

#Installation composer
if [ "$composer" = "oui" ]
then
    shexec install_composer $path_composer
fi

#Installation des Acl
if [ "$acl" = "oui" ]
then
    shexec add_acl
fi

#Installation du dossier web
if [ "$path_install" != "" ]
then
    [[ ! -d $path_install ]] && shexec mkdir -p $path_install
    shexec rsync -av $path_vagrant/ $path_install
    shexec chmod -R 755 $path_install
    shexec chown $web_user:$web_group $path_install
    shexec set_acl $path_install vagrant root vagrant 
fi

if [ "$symfony_install" = "oui" ]
then
    [[ ! -d $path_symfony ]] && mkdir -p $path_symfony
    path_symfony_base=$(readlink -f "/$path_symfony/..")
    shexec rm -rf $path_symfony
    echo $path_symfony_base
    shexec composer create-project -d $path_symfony_base  symfony/framework-standard-edition $path_symfony $symfony_version
    base_path=$(dirname $path_symfony/$path_symfony_lib)
    [[ ! -d $base_path ]] && shexec -p mkdir $base_path
    cp -rf $path_install/vagrant/resources/symfony/* $path_symfony

    shexec ln -s $path_install $path_symfony/$path_symfony_lib

    add_to_file /etc/fstab "/$path_symfony $path_install/vagrant/resources/symfony none bind"
    add_to_file /etc/fstab "/$path_symfony $path_install/vagrant/resources/symfony-temp none bind"
    shexec mount $path_install/vagrant/resources/symfony
    shexec mount $path_install/vagrant/resources/symfony-temp

    shexec set_acl $path_symfony/app/cache vagrant www-data vagrant 
    shexec set_acl $path_symfony/app/logs vagrant www-data vagrant
    shexec composer update
fi

if [ "$symfony" = "oui" ]
then 
    shexec set_acl $path_install/app/cache vagrant www-data vagrant 
    shexec set_acl $path_install/app/logs vagrant www-data vagrant    
fi

if [ "$zf2" = "oui" ]
then 
    shexec set_acl $path_install/data/cache vagrant www-data vagrant 
    shexec set_acl $path_install/app/logs vagrant www-data vagrant
fi

bash _end.sh

shexec restart_services $restart_services

rm $error

shexec link_system_files

shexec "apt-get clean  && apt-get autoclean"

echo Fin 2>/dev/null
echo $message
