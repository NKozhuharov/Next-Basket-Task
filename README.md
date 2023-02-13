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

* Enter inside the container with the run configuration **Container Console Users**. This will open the Users container
  in a terminal in PhpStorm.
* Inside the container, execute:

  ```sh
  cd /var/www/next-basket
  composer install
  php bin/console doctrine:migrations:migrate
  ```

#### Notifications Service

* Enter inside the container with the run configuration **Container Console Notifications**. This will open the Users
  container in a terminal in PhpStorm.
* Inside the container, execute:

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

#### To run the tests for Users Service

* Enter inside the container with the run configuration **Container Console Users**.
* Inside the container, execute:
  ```shell
    php bin/console --env=test doctrine:schema:create
    XDEBUG_MODE=coverage php bin/phpunit --coverage-html public/code_coverage
  ```
* This will generate a coverage report in the public directory, which can be opened by
  visiting http://127.0.0.1:80/code_coverage/Entity/index.html
    * I know it's wrong to place the coverage report in the public folder. I did it to avoid additional server
      configuration.
    * In production, it must be hidden, as it contains the code structure.

#### To run the tests for Notifications Service

* Enter inside the container with the run configuration **Container Console Notifications**.
* Inside the container, execute:
  ```shell
    XDEBUG_MODE=coverage php bin/phpunit --coverage-html public/code_coverage
  ```

#### Notes

* This is my first attempt to write an application in Symfony, I believe I understood the basics.
* I had to read through a lot of documentation and examples to put together the code. The solution is probably not the
  best (code quality, implementation), but it's my first attempt, and it's working, so I'm happy with it :)
* This is also my first attempt to use RabbitMQ, I've only used the PubSub messaging service before.
* I'm not sure about the tests, which I've provided, as I said, I only have experience in writing unit tests and here
  they are not exactly suitable.
* Overall, I've had fun and managed to learn some new things :)
