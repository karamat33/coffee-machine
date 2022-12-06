## Espress coffe machine

This api inludes 3 endpoints (check routes/api.php)):

- `api/status` `[GET]` : returns the status of the machine (`JSON` format)
- `api/make-one` `[POST]` : makes one espresso coffee and returns the status of the machine (`JSON` format)
- `api/make-double` `[POST]` : makes double espresso coffee and returns the status of the machine (`JSON` format)


## Technologies used

For this project I'm using:
- PHP 8.1
- Laravel 9
- Redis database for managing the state between the requests

### Steps to run the project

- run `composer install` to install the needed packages
- make sure you have redis installed on your machine and running
- configure the .env file with your redis credentials, host and port number
- run  `php artisan db:seed --class=ConfigSeeder` to seed the database with the initial values (50 spoons of beans and 2 litres of water)
you can change the values in the `database/seeders/ConfigSeeder.php` file
- run `php artisan serve` to start the server
- use postman or any other tool to test the endpoints
