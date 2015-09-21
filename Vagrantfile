system("
    if [ #{ARGV[0]} = 'up' ]; then
        echo 'Setting world write permissions for ./logs/*'
        chmod a+w ./logs
        chmod a+w ./logs/*
    fi
")

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "puppetlabs/centos-6.6-64-nocm"
  config.vm.network "private_network", ip: "192.168.50.50"

  # Make sure logs folder will be writable for Apache
  config.vm.synced_folder "logs", "/vagrant/logs", owner: 48, group: 48

  # Make sure downloads folder will be writable for Apache
  #config.vm.synced_folder "public/downloads", "/vagrant/public/downloads", owner: 48, group: 48

  # Install all needed packages
  config.vm.provision :shell, inline: "rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm"
  config.vm.provision :shell, inline: "rpm -Uvh http://download.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm"

  # Do not update for now because it breaks VBoxAdditions If you run this you need to
  # run $ /etc/init.d/vboxadd setup manually.
  #config.vm.provision :shell, inline: "yum -y update"

  # Make sure VBoxAdditions work after yum update
  #config.vm.provision :shell, inline: "/etc/init.d/vboxadd setup"

  # Continue with packages
  config.vm.provision :shell, inline: "yum -y install php56w php56w-opcache"
  config.vm.provision :shell, inline: "yum -y install php56w-pdo"
  config.vm.provision :shell, inline: "yum -y install php56w-mysqlnd"
  config.vm.provision :shell, inline: "yum -y install php56w-soap"
  config.vm.provision :shell, inline: "yum -y install php56w-mcrypt"
  config.vm.provision :shell, inline: "yum -y install php56w-mbstring"
  config.vm.provision :shell, inline: "yum -y install php56w-xml"
  # Enable if you want code coverage. Makes tests really slow.
  #config.vm.provision :shell, inline: "yum -y install php56w-pecl-xdebug"
  config.vm.provision :shell, inline: "yum -y install mod_ssl"

  # Install basic tools
  config.vm.provision :shell, inline: "yum -y install zsh"
  config.vm.provision :shell, inline: "yum -y install finger"
  config.vm.provision :shell, inline: "yum -y install telnet"
  config.vm.provision :shell, inline: "yum -y install screen"
  config.vm.provision :shell, inline: "yum -y install autossh"

  # Install node and npm so we can use later Grunt
  config.vm.provision :shell, inline: "yum -y install npm"

  # Install MySQL server
  config.vm.provision :shell, inline: "yum -y install mysql"
  config.vm.provision :shell, inline: "yum -y install mysql-server"
  config.vm.provision :shell, inline: "/etc/init.d/mysqld restart"
  config.vm.provision :shell, inline: "/sbin/chkconfig --levels 235 mysqld on"

  # Stop iptables because it is not really needed on development environment
  config.vm.provision :shell, inline: "/etc/init.d/iptables stop"
  config.vm.provision :shell, inline: "/sbin/chkconfig iptables off"

  # Update Apache config
  # Make /vagrant/public as document root
  config.vm.provision :shell, inline: 'sed -i -e "s/DocumentRoot \"\/var\/www\/html\"/DocumentRoot \/vagrant\/public/" /etc/httpd/conf/httpd.conf'
  # Or alternatively use directory alias instead of document root.
  #config.vm.provision :shell, inline: 'echo "Alias /api/ /vagrant/public/" >> /etc/httpd/conf/httpd.conf'
  config.vm.provision :shell, inline: 'echo "EnableSendfile off" >> /etc/httpd/conf/httpd.conf'
  config.vm.provision :shell, inline: 'sed -i -e "s/ErrorLog logs\/error_log/ErrorLog \/vagrant\/logs\/error_log/" /etc/httpd/conf/httpd.conf'
  config.vm.provision :shell, inline: 'sed -i -e "s/CustomLog logs\/access_log/CustomLog \/vagrant\/logs\/access_log/" /etc/httpd/conf/httpd.conf'
  config.vm.provision :shell, inline: 'sed -i -e "s/AllowOverride None/AllowOverride All/" /etc/httpd/conf/httpd.conf'

  # Restart Apache for first time
  config.vm.provision :shell, inline: "/etc/init.d/httpd restart"
  config.vm.provision :shell, inline: "/sbin/chkconfig --levels 235 httpd on"

  # Make sure Apache also runs after vagrant reload
  $upstart = <<UPSTART
echo "
# Start Apache after /vagrant is mounted
start on vagrant-mounted
exec /etc/init.d/httpd restart
" > /etc/init/httpd.conf
UPSTART
  config.vm.provision :shell, inline: $upstart

  # Install Composer and dependencies
  config.vm.provision :shell, inline: "curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer"
  config.vm.provision :shell, inline: "cd /vagrant && /usr/local/bin/composer install"

  # Install Grunt and npm dependencies
  config.vm.provision :shell, inline: "npm install -g grunt-cli"
  #config.vm.provision :shell, inline: "cd /vagrant && npm install"

  # Create environent file
  config.vm.provision :shell, inline: "cd /vagrant && cp .env.example .env"

  # Create and migrate database
  config.vm.provision :shell, inline: 'echo "CREATE DATABASE example;" | mysql -u root'
  #config.vm.provision :shell, inline: "cd /vagrant && bin/db migrate"

  $message = <<MESSAGE

  ███████╗██╗     ██╗███╗   ███╗██████╗
  ██╔════╝██║     ██║████╗ ████║╚════██╗
  ███████╗██║     ██║██╔████╔██║ █████╔╝
  ╚════██║██║     ██║██║╚██╔╝██║ ╚═══██╗
  ███████║███████╗██║██║ ╚═╝ ██║██████╔╝
  ╚══════╝╚══════╝╚═╝╚═╝     ╚═╝╚═════╝

 You can access me at: https://192.168.50.50/

MESSAGE

  config.vm.post_up_message = $message
end