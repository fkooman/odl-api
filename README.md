# Introduction
This is software for using the ODL API.

## Requirements
### Ubuntu
Install [Composer](https://getcomposer.org).

### Fedora
Install Composer and some other dependencies:

    $ sudo dnf -y install composer git php httpd mod_ssl

# Installation

    $ cd /var/www
    $ sudo mkdir odl-api
    $ sudo chown fkooman.fkooman odl-api
	$ git clone https://github.com/fkooman/odl-api.git
	$ cd odl-api
	$ composer install

Also configure Apache, put this in `/etc/httpd/conf.d/odl-api.conf`. Do not 
forget to allow access from a location of your choice.

    Alias /odl-api /var/www/odl-api/web

    <Directory /var/www/odl-api/web>
        AllowOverride None

        Require local
        #Require all granted

        # eduVPN
        #Require ip 195.169.120.0/23
        #Require ip 2001:0610:0450:4242::/64

        RewriteEngine On
        RewriteBase /odl-api
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php/$1 [QSA,L]

        SetEnvIfNoCase ^Authorization$ "(.+)" HTTP_AUTHORIZATION=$1
    </Directory>

# Configuration

    $ cp config/config.ini.example config/config.ini

Now modify the `config.ini` file.

## Flows
Create a `data` directory in `/var/www/odl-api` and put there the flow 
directories. For example:

    /var/www/odl-api/data/loop/01.json
    /var/www/odl-api/data/loop/02.json
    /var/www/odl-api/data/loop/03.json

The UI will show a 'Loop' button and execute the API calls with data from 
`01.json`, `02.json` and `03.json`, sorted by name. Adding more directories
will show more buttons.

# SELinux
You may need to allow `httpd` to connect to the network to connect to the 
API:

    $ sudo setsebool -P httpd_can_network_connect=on
