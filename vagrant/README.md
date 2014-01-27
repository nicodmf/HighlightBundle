What does it do?
================

This vagrant configuration sets up a basic LAMP environment suited for Symfony 2 development.

Prerequisites
=============

Install Vagrant
---------------

Obviously, you need [Vagrant](http://www.vagrantup.com/), which in turn requires Ruby and
VirtualBox. Vagrant runs on Linux, OS X, and Windows, although some special configuration applies
to Windows (see below).

Install VirtualBox
------------------

Install [VirtualBox](https://www.virtualbox.org/), the easiest way is to install the last version 
and the Extension Pack dedicated. 


Start your new environment
--------------------------------------------------------

    $ vagrant up

Depending on the versions of the box and your VirtualBox installation, you might see a notice that
the guest additions of the box do not match the version of VirtualBox you are using. If you
encounter any problems, you might want to install up to date guest additions on your box once
running and [repackage it for use with Vagrant](http://vagrantup.com/docs/getting-started/packaging.html).

Use it
------

### Webserver

The app is now accessible from your host system at [33.33.33.99](http://33.33.33.99/app_dev.php).

### MySQL

The setup will configure MySQL with a user/password of root/root.
PHPMyAdmin is at [phpmyadmin](http://33.33.33.99/phpmyadmin).

### SSH and the Symfony console

Connect to your virtual machine:

    $ vagrant ssh

Or by a ssh console

    $ ssh vagrant@33.33.33.100
    le mot de passe est vagrant

Or with putty

    $ ssh vagrant@33.33.33.100
    le mot de passe est vagrant

Change to your project directory and launch the Symfony shell:

    vagrant@vagrantup:~$ cd /var/www/vhost
    vagrant@vagrantup:~$ app/console -s

Notes
-----

If you want to use multiple instances of this virtual machine at the same time on a single host, you 
will need to edit the IP set in `mydir/Vagrantfile` to avoid conflicts.


### Vagrant up after a destroy


If you have a problem like that after a vagrant destroy and a vagrant up, it's usual, just remake an
up: 

    Bringing machine 'default' up with 'virtualbox' provider...
    There was an error while executing `VBoxManage`, a CLI used by Vagrant
    for controlling VirtualBox. The command and stderr is shown below.

    Command: ["list", "hostonlyifs"]

    Stderr: VBoxManage.exe: error: Failed to create the VirtualBox object!
    VBoxManage.exe: error: Code CO_E_SERVER_EXEC_FAILURE (0x80080005) - Server execu
    tion failed (extended info not available)
    VBoxManage.exe: error: Most likely, the VirtualBox COM server is not running or
    failed to start.

### Problem with webserver after reboot vm (version from 07.01.2013 to 15.01.2013)

Make a
 
    vagrant destroy
    vagrant up 

Or speedest solution, just launch the next command in a root logged shell : 

    for i in $(find /vagrant/vagrant/resources/system/ -type f); do a=$(echo $i| sed -e "s/\/vagrant\/vagrant\/resources\/system//g";); echo $a; rm $a; cp  /vagrant/vagrant/resources/system/$a $a; done


### Problem with sudo and/or apache (Could not reliably determine the server's fully qualified domain name)

Add at the beginning of the file /etc/hosts, the line 
    
    127.0.0.1 symfony-cloud  

### Problem with composer : Authentication required (api.github.com)

To execute the command :

    composer config -g github-oauth.github.com <yourgithubtoken>
    
This token works :

    composer config -g github-oauth.github.com 5103b9b7ada3bebccb5c8d2a420a8ce6aa5bf1bb
    



