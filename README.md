## Project configuration

The following instructions are how to set up the project on Ubuntu.
The setup assumes that the following software is already installed:

* Docker
* GIT
* PhpStorm

#### To deploy the project locally (on Ubuntu)

* Navigate to desired folder and execute

```shell
    cd next_basket_task
    git config core.fileMode false
    cd docker/images/next-basket
    docker build --tag next-basket/php-apache:latest .
    docker pull mysql:latest
    docker pull rabbitmq
    docker pull rabbitmq:3-management
    
    cd users
    cp .env.local.example .env.local
    
    cd ..
    
    cd notifications
    cp .env.local.example .env.local
```

* Open PhpStorm, choose "Create a project from the existing files"
* Run the run configuration **Run Services** to create apache and database containers.
* Wait approximately 10 seconds for the database container to initialize.

#### Users Service

* Enter inside the container with the run configuration **Container Console Users**. This will open the Users container in a terminal in PhpStorm.
* From inside the container, execute:

  ```sh
  cd /var/www/next-basket
  composer install
  php bin/console doctrine:migrations:migrate
  ```

#### Notifications Service

* Enter inside the container with the run configuration **Container Console Notifications**. This will open the Users container in a terminal in PhpStorm.
* From inside the container, execute:

  ```sh
  cd /var/www/next-basket
  composer install
  php bin/console messenger:consume -vv user_messages
  ```

#### RabbitMQ
* To access the administrative panel, open http://localhost:8080/
