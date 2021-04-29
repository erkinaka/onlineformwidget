# Online Form Widget

Build and publish online forms.

## Requirements:

* Php  ^7.0
* Mysql
* Laminas Framework Configuration


## Used Technologies:

* Laminas Framework -  https://github.com/laminas
* Form Builder Javascrip - https://github.com/kevinchappell/formBuilder
* PHP Excel - https://github.com/PHPOffice/PhpSpreadsheet


## About

It is a complete web site to build online forms and publish like Google Forms. Best side of us is, you can host site on your own servers. This site has a simple form manage interface.
After creating and publishing forms, you can collect and report results.

## Install

1. First of all this Project is based on Laminas MVC and you should maket he Laminas server configurations as at https://docs.laminas.dev/tutorials/getting-started/skeleton-application/#using-the-apache-web-server.

1. Clone or download the repository to your computer. 

1. To download neccessary packages you should run the following code in a terminal at your project directory. (You will need to install composer.) This command will create vendor folder and install Laminas and other packages.

####'composer.install'

1. For database, you should use sql file which is in the project’s database directory.

1. You should change the following lines with your configuration which are in config/autoload/global.php

$dbParams = array(
    * 'database' => 'onlineformwidget',
    * 'username' => 'root',
    * 'password' => '',
    * 'hostname' => 'localhost'
);

## Live Demo
* https://sihirliform.com

## Screenshots

### Manage Forms

![Manage Forms](https://github.com/erkinaka/onlineformwidget/blob/main/screenshots/img1.png?raw=true)


### Add Form

![Add Form](https://github.com/erkinaka/onlineformwidget/blob/main/screenshots/img2.png?raw=true)


 


