# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "vagrant-debian-wheezy64"
  config.vm.box_url = "https://dl.dropboxusercontent.com/s/xymcvez85i29lym/vagrant-debian-wheezy64.box"

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", 812]
    vb.customize ["modifyvm", :id, "--cpus", "2"] 
    vb.customize ["modifyvm", :id, "--ioapic", "on"]
  end

  config.vm.network :private_network, ip: "33.33.33.150"

  #config.vm.provision :puppet, :options => "--verbose" do |puppet|
  #  puppet.manifests_path = "vagrant/manifests"
  #  puppet.manifest_file = "up.pp"
  #end

  config.vm.provision "shell", path: "vagrant/bin/provision.sh"

  #config.vm.synced_folder("vagrant-root", "/vagrant", ".", :nfs => true, :create => true)
  #config.vm.synced_folder("vagrant-root", "/vagrant")

end
