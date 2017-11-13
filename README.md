Installation du projet.
========================
Cloner le projet
------------------------
* Installer git bash for windows

* git bash on www repository

* run :  git clone https://github.com/talanon/consso.git

Installer composer sur windows
-------------------------------

https://www.hostinger.fr/tutoriels/installer-utiliser-composer/

(N'oubliez pas de relancer git bash apres avoir installer composer)

Initialiser le projet
-------------------------

* git bash to the consso repository
* run : composer install
* During composer : they will ask you to parameter database. Just go to phpmyadmin and create a database called consso. Then let all parameters by default and just modify le name of database (was symfony, just write consso).
* Now we will set some alias that will save some times. In bash just run :

alias sf2=bin/console

alias sfcc='bin/console cache:clear'

Ok run sf2 to see all possible command.
When you need to clear cache just run sfcc.
If alias do not work after setting them just reload git bash.

* run : sf2 doctrine:schema:update --force
* If you are using wamp or easyPHP : rename the folder of the project to : local.www.consso.fr
* Now go to localhost and add a virtualhost.

Name of the virtualhost :local.www.consso.fr

Absolute path : (should be like that, modify if your path is different) c:/wamp64/www/local.www.consso.fr/web

* Restart apache. If you are using wamp or easyPHP you can use the software tool Reload DNS.

* Open your browser and type http://local.www.consso.fr/. Everything should be ok to start.

* If you want to dev with the debug mode just write app_dev.php after the url. (http://local.www.consso.fr/app_dev.php/)

(Don't forget 2 point, develop on the develop branch or create a feature. And don't forget to reopen git on the new repository.)

Symfony Standard Edition
========================

Welcome to the Symfony Standard Edition - a fully-functional Symfony
application that you can use as the skeleton for your new applications.

What's inside?
--------------

  * **FrameworkBundle** - The core Symfony framework bundle

  * [**SensioFrameworkExtraBundle**][6] - Adds several enhancements, including
    template and routing annotation capability

  * [**DoctrineBundle**][7] - Adds support for the Doctrine ORM

  * [**TwigBundle**][8] - Adds support for the Twig templating engine

  * [**SecurityBundle**][9] - Adds security by integrating Symfony's security
    component

  * [**SwiftmailerBundle**][10] - Adds support for Swiftmailer, a library for
    sending emails

  * [**MonologBundle**][11] - Adds support for Monolog, a logging library

  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar

  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for
    configuring and working with Symfony distributions

  * [**SensioGeneratorBundle**][13] (in dev/test env) - Adds code generation
    capabilities

  * **DebugBundle** (in dev/test env) - Adds Debug and VarDumper component
    integration


[1]:  https://symfony.com/doc/3.2/setup.html
[6]:  https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html
[7]:  https://symfony.com/doc/3.2/doctrine.html
[8]:  https://symfony.com/doc/3.2/templating.html
[9]:  https://symfony.com/doc/3.2/security.html
[10]: https://symfony.com/doc/3.2/email.html
[11]: https://symfony.com/doc/3.2/logging.html
[12]: https://symfony.com/doc/3.2/assetic/asset_management.html
[13]: https://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html
