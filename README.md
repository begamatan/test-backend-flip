# Disbursement service

## Note
If your database did'nt allow `0000-00-00 00:00:00` for default timestamp value, the value will be current timestamp when record is created.

## Requirement
- PHP (tested on PHP 7.4)
- MySQL (tested on MySQL 5.7)
- PHP PDO and PDO MySQL enabled
- PHP Curl enabled

## Setup and Usage
- Copy `config.php.example` as `config.php` file, if you're using linux/mac that have shell access, enter this project directory and run `cp config.php.example config.php`
- You can configure your database credential and API secret key provided by `Slightly-big Flip` in `config.php` file
- After that, you need to run `php migrate.php` to migrate table needed to use this service
- Run `php index.php` to use the service, you will be prompted several question based on service you want to use.
