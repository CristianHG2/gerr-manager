<?php

function install()
{
    IO::log('Verifying LEMP stack');

    if (`which php`) {
        IO::log('PHP OK');
    
        if (IO::confirm('Update PHP to PHP 7.3?')) {
            exec('sudo add-apt-repository ppa:ondrej/php');
            exec('sudo apt-get update');
            exec('sudo apt-get install php7.3 php7.3-fpm phpcli phpmysql phpgd phpimagick phprecode phptidy phpxmlrpc');
        }
    } else {
        if (IO::confirm('PHP 7.3 (FPM) NOT installed, install?')) {
            exec('sudo add-apt-repository ppa:ondrej/php');
            exec('sudo apt-get update');
            exec('sudo apt-get install php7.3 php7.3-fpm phpcli phpmysql phpgd phpimagick phprecode phptidy phpxmlrpc');
        } else {
            IO::abort();
        }
    }

    if (`which nginx`) {
        IO::log('NGINX OK');
    
        if (IO::confirm('Update nginx?')) {
            exec('sudo add-apt-repository ppa:nginx/stable');
            exec('sudo apt-get update');
            exec('sudo apt-get install nginx');
        }
    } else {
        if (IO::confirm('Nginx NOT installed, install?')) {
            exec('sudo add-apt-repository ppa:nginx/stable');
            exec('sudo apt-get update');
            exec('sudo apt-get install nginx');
        } else {
            IO::abort();
        }
    }

    if (`which composer`) {
        IO::log('Composer OK');

        if (IO::confirm('Update composer?')) {
            exec('composer self-update');
        }
    } else {
        if (IO::confirm('Composer NOT installed, install?')) {
            $installer = file_get_contents('https://getcomposer.org/installer');
	    file_put_contents('composer-install.php', $installer);

	    exec('php composer-install.php');
	    exec('sudo mv composer.phar /usr/local/bin/composer');

	    IO::log('Composer installed');
        } else {
            IO::abort();
        }
    }

    if (`which mysql`) {
        IO::log('MySQL/MariaDB OK');
        IO::log('GERR doesn not support updating MySQL. Please update manually if applicable');
    } else {
        if (IO::confirm('MySQL/MariaDB NOT installed, install?')) {
            exec('sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8');
            exec('sudo add-apt-repository \'deb [arch=amd64,arm64,ppc64el] http://mirrors.coreix.net/mariadb/repo/10.4/ubuntu bionic main\'');
            exec('sudo apt-get update');
            exec('sudo apt-get install mariadb-server');
            IO::log('Please enter the username in config.json');
        } else {
            IO::abort();
        }
    }

    if (!file_exists('/usr/bin/gerr')) {
        if (IO::confirm('Make "gerr" executable globally available?')) {
            exec('sudo ln -s '.__DIR__.'/gerr.php /usr/bin/gerr');
        }
    }

    if (`which certbot`) {
	IO::log('Certbot OK');

	if (IO::confirm('Update Certbot?')) {
	    passthru('sudo apt-get update');
	    passthru('sudo apt-get install --only-upgrade certbot');
	}
    } else {
	passthru('bash '.__DIR__.'/bash/certbot.sh');
	IO::log('Certbot installed');
    }
}
