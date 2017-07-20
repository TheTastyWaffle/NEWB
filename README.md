newb
====

A Symfony project created on June 14, 2017, 4:28 pm.
## Available at : http://127.0.0.1:8080OA

### Init projet:
    $ git clone https://GrasQuentin@bitbucket.org/GrasQuentin/news.git
    $ cd news/
    $ composer update
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force

### Run
    $ php bin/console server:run

### Testing
Create a database :
    dbname: testDB
    user: root

    $ wget https://phar.phpunit.de/phpunit-6.1.phar
    $ chmod +x phpunit-6.1.phar
    $ sudo mv phpunit-6.1.phar /usr/local/bin/phpunit
    $ phpunit --version
    $ phpunit


## Run with Docker
Download and install Docker : https://www.docker.com

### Init project :

    $ cd docker
    $ docker-composer up -d
    $ docker-composer exec php bash
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force

### Stop server :

    $ docker-composer down

### Run :

    $ docker-composer up -d
