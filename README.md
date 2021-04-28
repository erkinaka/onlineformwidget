Online Form Widget
Build and publish online forms.
Requirements:
Php  ^7.0
Mysql
Used Technologies:
Laminas Framework -  github.com/laminas
Form Builder Javascrip - github.com/kevinchappell/formBuilder
PHP Excel - github.com/PHPOffice/PhpSpreadsheet
About
It is a complete web site to build online forms and publish like Google Forms. Best side of us is, you can host site on your own servers. This site has a simple form manage interface.
After creating and publishing forms, you can collect and report results.
Install

1-	First of all this Project is based on Laminas MVC and you should maket he Laminas server configurations as at https://docs.laminas.dev/tutorials/getting-started/skeleton-application/#using-the-apache-web-server.
2-	Clone or download the repository to your computer. 
3-	To download neccessary packages you should run the following code in a terminal at your project directory. (You will need to install composer.)
4-	For database, you should use sql file which is in the project’s database directory.
5-	You should change the following lines with your configuration which are in config/autoload/global.php
$dbParams = array(
    'database' => 'onlineformwidget',
    'username' => 'root',
    'password' => '',
    'hostname' => 'localhost'
);
Screenshots
![alt text](https://github.com/[erkinaka]/[ onlineformwidget]/blob/[branch]/image.jpg?raw=true)

![alt text](https://github.com/[username]/[reponame]/blob/[main]/image.jpg?raw=true)


 


