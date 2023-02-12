## Project configuration

The following instructions are how to set up the project on Ubuntu.
The setup assumes that the following software is already installed:

* Docker
* GIT
* PhpStorm

#### To deploy the project locally (on Ubuntu)

* Navigate to desired folder and execute

```shell
    git clone https://github.com/NKozhuharov/Next-Basket-Task.git
    cd Next-Basket-Task
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

#### Creating a new User
* Go to https://documenter.getpostman.com/view/8850308/2s935uGg1c and click **Run in Postman** button
* Navigate to the **Next Basket Task** collection
* Execute **Create User** route
  * The User Service will create a new User in the database
  * A message will be pushed to the RabbitMQ, which will be consumed by the Notifications Service
  * In the terminal, name **Container Console Notifications**, the logged output can be seen
  * The contents of the whole log file can be verified in the same terminal:
  ```shell
    cd /var/www/next-basket/var/log
    cat local.log
  ```
  * The API endpoint will return a JSON response with the attributes of the user
