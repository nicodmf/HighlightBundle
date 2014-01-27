#!/bin/bash

function loggableexec()
{
    $*
}

function realexec()
{
    loggableexec "$*" 2>$error
}

function shexec()
{
    echo "- "$*
    echo >> $log
    echo "Début "$* >> $log
    realexec "$*" >> $log
    [[ "$verbose" = "oui" ]] && cat $error
    cat $error >> $log
    echo "Fin "$* >> $log
    echo >> $log
    echo "" > $error
}

function add_lib_keys()
{
    for lib in $*
    do
        wget $lib -O lib.key.gpg
        apt-key add lib.key.gpg
    done
    rm lib.key.gpg
}
function install_pecl_libs()
{
    apt-get -q -y install php-pear php5-dev
    for lib in $*
    do
        pecl install $lib
    done
}

function install_libs()
{
    export DEBIAN_FRONTEND=noninteractive
    apt-get update
    [[ "with_upgrade" = "oui" ]] && apt-get -q -y upgrade
    apt-get -q -y install debconf-utils
    for file in /vagrant/vagrant/resources/debconf
    do
        cat $file | debconf-set-selections
    done
    apt-get -q -y install $*
}

function copy_init_system_files()
{
    for i in $*
    do
        [[ -f "$i" ]] && rm $i
        cp /vagrant/vagrant/resources/system$i $i
    done
}

function prepare_link_system_files()
{
    add_to_file /etc/fstab / $path_install/vagrant/resources/system none bind
    umount $path_install/vagrant/resources/system
}

function link_system_files()
{
    mount $path_install/vagrant/resources/system
}

function copy_sys_files()
{
    for i in $(find /vagrant/vagrant/resources/system/ -type f| sed -e "s/\/vagrant\/vagrant\/resources\/system//g")
    do
        [[ -f "$i" ]] && rm $i
        cp /vagrant/vagrant/resources/system$i $i
    done
}

function add_confs()
{
    for i in $*
    do
        add_to_file $i $(cat /vagrant/vagrant/resources/system$i)
    done
}

function add_to_file()
{
    file=$1
    shift
    add=$*
    echo "file=$file add=$add"
    if [[ "" = "$(cat $file | grep '$add')" ]]
    then
        echo $add >> $file
    fi
}

function replace_in_file()
{
    local file=$1
    local pattern=$2
    local replacement=$3

    local tmp_file=/tmp/file

    cat $file | sed -e s/$pattern/$replacement/g > $tmp_file
    echo "cat $file | sed -e s/$pattern/$replacement/g > $tmp_file"
    mv $tmp_file $file
}

function restart_services(){
    for i in $*
    do
        service $i restart
    done
}

function add_acl()
{
    #Ajoute les acl au fichier /etc/fstab et remonte la partition
    if [[ "$(cat /etc/fstab|grep acl)" = "" ]]
    then
        cat /etc/fstab| sed -e "s/errors=remount-ro/errors=remount-ro,acl/" > tmp_fstab
        mv tmp_fstab /etc/fstab
    fi
    mount -o remount,acl /
}

function install_composer()
{
    local path_composer=$1
    set|grep http
    curl -sS http://getcomposer.org/installer | php -- 
    mv ./composer.phar $path_composer
    chmod +x $path_composer 2>&1

    if [[ "$github_oauth" != "" ]]
    then
        composer config -g github-oauth.github.com $github_oauth
    fi
}

function reset_mysql_password(){
    service mysql stop
    mysqld_safe --skip-grant-tables &
    sleep 3
    echo "update user set Password=PASSWORD('root') where user='root'; flush privileges;" > sql_temp
    mysql --user=root mysql < sql_temp && rm sql_temp
    killall -9 mysqld_safe 2>&1
    service mysql start
}

function create_user_e_db()
{
    local db=$1
    local ut=$2
    local ps=$3

    mysqladmin -uroot -proot create $db 2>&1
    create_dbuser $db $ut $ps
}

function create_dbuser()
{
    local db=$1
    local ut=$2
    local ps=$3
    local sql=tmpfile
    [[ "$db" == "" ]] && db="*"

    echo "grant all privileges on $db.* to '$ut'@localhost" > $sql
    [[ "$ps" != "" ]] && echo "identified by '$ps'" >> $sql
    echo ";" >> $sql
    mysql -uroot -proot < $sql 2>&1
    rm $sql;
}

#Ajoute les acls et configure les droits des fichiers
function set_acl()
{
    local dir=$1
    local u1=$2
    local u2=$3
    local gr=$4
    [[ ! -d $dir ]] && mkdir -p $dir 2>&1
    setfacl -R -m u:$u1:rwx -m u:$u2:rwx -m g:$gr:rwx $dir
    setfacl -dR -m u:$u1:rwx -m u:$u2:rwx -m g:$gr:rwx $dir
}

function installSSL(){

    #provide subject as first value like that :
    # sbj="
    # C=$country
    # ST=$state
    # O=$company
    # localityName=$locality
    # commonName=$DOMAIN
    # organizationalUnitName=$departementName
    # emailAddress=$email
    # "
    
    sbj=$*

    [[ ! -d "/etc/apache2/ssl/" ]] && mkdir /etc/apache2/ssl/
    openssl req -new -x509 -days 3650 -nodes \
        -out /etc/apache2/ssl/apache.pem \
        -keyout /etc/apache2/ssl/apache.pem \
        -batch \
        -subj "/$(echo -n "$subj" | tr " " "/")" \
    
}