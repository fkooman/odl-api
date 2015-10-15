# Introduction
This is software for using the ODL API.

## Requirements
### Ubuntu
Install [Composer](https://getcomposer.org).

### Fedora
Install Composer:

    $ sudo dnf -y install composer

# Installation

	$ git clone https://github.com/fkooman/odl-api.git
	$ cd odl-api
	$ composer install

# Configuration

    $ cp config/config.ini.example config/config.ini

Now modify the `config.ini` file.

# Running
	
	$ php bin/f1.php

