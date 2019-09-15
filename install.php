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
        } else {
            if (IO::confirm('PHP 7.3 (FPM) NOT installed, install?')) {
                exec('sudo add-apt-repository ppa:ondrej/php');
                exec('sudo apt-get update');
                exec('sudo apt-get install php7.3 php7.3-fpm phpcli phpmysql phpgd phpimagick phprecode phptidy phpxmlrpc');
            } else {
                IO::abort();
            }
        }
    }

    if (`which nginx`) {
        IO::log('NGINX OK');
    
        if (IO::confirm('Update nginx?')) {
            exec('sudo add-apt-repository ppa:nginx/stable');
            exec('sudo apt-get update');
            exec('sudo apt-get install nginx');
        } else {
            if (IO::confirm('Nginx NOT installed, install?')) {
                exec('sudo add-apt-repository ppa:nginx/stable');
                exec('sudo apt-get update');
                exec('sudo apt-get install nginx');
            } else {
                IO::abort();
            }
        }
    }

    if (`which composer`) {
        IO::log('Composer OK');

        if (IO::confirm('Update composer?')) {
            exec('composer self-update');
        }
    } else {
        if (IO::confirm('Composer NOT installed, install?')) {
            $string = '
                php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"
                    php -r "if (hash_file(\'sha384\', \'composer-setup.php\') === \'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1\') { echo \'Installer verified\'; } else { echo \'Installer corrupt\'; unlink(\'composer-setup.php\'); } echo PHP_EOL;"
                    php composer-setup.php
                php -r "unlink(\'composer-setup.php\');"
            ';

            exec($string);
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
}