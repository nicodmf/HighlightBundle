cd /vagrant/vagrant/bin
source _config.sh
source utils.sh

shexec service apache2 restart

#ln -s $path_install/vendor/knplabs/knp-console-autocomplete-bundle/Knp/Bundle/ConsoleAutocompleteBundle/Resources/Shells/symfony2-completion.bash /etc/bash_completion.d/

#shexec $path_install/bin/install.sh