# vehicle_reservation
## Prerequisites:

- **[PHP](https://www.php.net/)**
- **[MYSQL](https://www.mysql.com/)**
- **[Composer](https://getcomposer.org/)**
- **[Symfony CLI](https://symfony.com/download#step-1-install-symfony-cli)**

## How to run application:

1. clone this repository to your computer or download it as .ZIP
2. setup your MYSQL server, create `.env.local` file and put MYSQL server url as `DATABASE_URL`
3. install dependencies `composer install`
4. run app with `symfony server:start`, you can use optional `--port=` or `-d` to run in detached state
5. populate DB with test data `php bin/console doctrine:fixtures:load`

