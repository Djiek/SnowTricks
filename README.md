# SnowTricks
SnowTricks is a community site for learning snowboard tricks.
***
* [![Maintainability](https://api.codeclimate.com/v1/badges/ced261c97cadbf5068ac/maintainability)]
* (https://codeclimate.com/github/Djiek/SnowTricks/maintainability)
***
## Technologies
***
A list of technologies used within the project:
* [Symfony]: Version 4.4.16
* [PHP]: Version 7.3.12
* [bootstrap]: Version 4.2.1
* [Doctrine]
* [twig]
* [html]
* [css]
* [javascript]
* [jquery]
* [git]  
* [mySql] 
* [composer]
***

## Installation
***
SnowTricks requires php 7.3.12 to run.
To install the project :

* To download the project, please clone the github project with the repository link :
```$ git clone https://github.com/Djiek/SnowTricks```
* Update your BDD credentials in SnowTricks .env
* Update your mailer_url : ex : MAILER_URL=gmail://name@gmail.com:password@localhost?encryption=tls&auth_mode=oauth
```
$ composer install
$ php bin/console doctrine:database:create 
$ php bin/console doctrine:migrations:migrate
$ php bin/console doctrine:fixtures:load  
$ php bin/console server:run
```
