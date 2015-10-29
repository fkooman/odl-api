# Introduction
This is software for using the ODL API.

## Requirements
### Ubuntu
Install [Composer](https://getcomposer.org).

### Fedora
Install Composer:

    $ sudo dnf -y install composer

# Installation

    $ cd /var/www
    $ sudo mkdir odl-api
    $ sudo chown fkooman.fkooman odl-api
	$ git clone https://github.com/fkooman/odl-api.git
	$ cd odl-api
	$ composer install

# Configuration

    $ cp config/config.ini.example config/config.ini

Now modify the `config.ini` file.

# Apache

    Alias /odl-api /var/www/odl-api/web

    <Directory /var/www/odl-api/web>
        AllowOverride None

        Require local
        #Require all granted

        RewriteEngine On
        RewriteBase /odl-api
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php/$1 [QSA,L]

        SetEnvIfNoCase ^Authorization$ "(.+)" HTTP_AUTHORIZATION=$1
    </Directory>

