# Fix permissions after you run commands on both hosts and guest machine
if !Vagrant::Util::Platform.windows?
  system("
      if [ #{ARGV[0]} = 'up' ]; then
          echo 'Setting group write permissions for ./logs/*'
          chmod 775 ./logs
          chmod 664 ./logs/*
      fi
  ")
end

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "puppetlabs/centos-6.6-64-nocm"
  config.vm.network "private_network", ip: "192.168.50.50"

  # Make sure logs folder will be writable for Vagrant and Apache
  config.vm.synced_folder "logs", "/vagrant/logs", owner: 500, group: 48

  # Make sure downloads folder will be writable for Apache
  #config.vm.synced_folder "public/downloads", "/vagrant/public/downloads", owner: 48, group: 48

  # Install all needed packages
  config.vm.provision "shell", name: "rpm", inline: <<-SHELL
    rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
    rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm
  SHELL

  # PHP and modules
  config.vm.provision "shell", name: "php", inline: <<-SHELL
    yum -y install mod_php71w php71w-opcache
    yum -y install php71w-cli
    yum -y install php71w-common
    yum -y install php71w-mysqlnd
    yum -y install php71w-pdo
    yum -y install php71w-xml
    yum -y install mod_ssl
  SHELL

  # Use the provided example environment
  config.vm.provision "shell", name: "environment", inline: <<-SHELL
    cd /vagrant && cp .env.example .env
  SHELL

  # Uncomment to install basic tools
  #config.vm.provision "shell", name: "tools", inline: <<-SHELL
  #  yum -y install zsh
  #  yum -y install finger
  #  yum -y install telnet
  #  yum -y install screen
  #SHELL

  # MariaDB
  config.vm.provision "shell", name: "mariadb", inline: <<-SHELL
  cat <<EOF | sudo tee /etc/yum.repos.d/influxdb.repo
[mariadb]
name = MariaDB
baseurl = http://yum.mariadb.org/10.0/centos6-amd64
gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
gpgcheck=1
EOF
    yum -y install MariaDB-server
    yum -y install MariaDB-client
    /sbin/service mysql start
    /sbin/chkconfig --levels 235 mysql on
    echo "CREATE DATABASE example" | mysql -u root
    cd /vagrant && bin/db migrate
  SHELL

  # Update Apache config and restart
  config.vm.provision "shell", name: "apache", inline: <<-'SHELL'
    sed -i -e "s/DocumentRoot \"\/var\/www\/html\"/DocumentRoot \/vagrant\/public/" /etc/httpd/conf/httpd.conf
    # If you prefer Slim app to be in subfolder comment above and uncomment below
    #echo "Alias /foobar/ /vagrant/public/" >> /etc/httpd/conf/httpd.conf
    echo "EnableSendfile off" >> /etc/httpd/conf/httpd.conf
    sed -i -e "s/ErrorLog logs\/error_log/ErrorLog \/vagrant\/logs\/error_log/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/CustomLog logs\/access_log/CustomLog \/vagrant\/logs\/access_log/" /etc/httpd/conf/httpd.conf
    sed -i -e "s/AllowOverride None/AllowOverride All/" /etc/httpd/conf/httpd.conf

    sed -i -e "s/ErrorLog logs\/ssl_error_log/ErrorLog \/vagrant\/logs\/error_log/" /etc/httpd/conf.d/ssl.conf
    sed -i -e "s/TransferLog logs\/ssl_access_log/TransferLog \/vagrant\/logs\/access_log/" /etc/httpd/conf.d/ssl.conf

    /etc/init.d/httpd restart
    /sbin/chkconfig --levels 235 httpd on

    # Make sure Apache also runs after vagrant reload
    echo "# Start Apache after /vagrant is mounted" > /etc/init/httpd.conf
    echo "start on vagrant-mounted" >> /etc/init/httpd.conf
    echo "exec /etc/init.d/httpd restart" >> /etc/init/httpd.conf
  SHELL

  # Stop iptables because it causes too much confusion
  config.vm.provision "shell", name: "iptables", inline: <<-SHELL
    /etc/init.d/iptables stop
    /sbin/chkconfig iptables off
  SHELL

  # Install Composer and dependencies
  config.vm.provision "shell", name: "composer", inline: <<-SHELL
    curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
    cd /vagrant && /usr/local/bin/composer install
  SHELL

  config.vm.post_up_message = <<MESSAGE

  ███████╗██╗     ██╗███╗   ███╗██████╗
  ██╔════╝██║     ██║████╗ ████║╚════██╗
  ███████╗██║     ██║██╔████╔██║ █████╔╝
  ╚════██║██║     ██║██║╚██╔╝██║ ╚═══██╗
  ███████║███████╗██║██║ ╚═╝ ██║██████╔╝
  ╚══════╝╚══════╝╚═╝╚═╝     ╚═╝╚═════╝

 You can access me at: https://192.168.50.50/

MESSAGE

end