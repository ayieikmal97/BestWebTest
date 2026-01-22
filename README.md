# Laravel Project

This is a Laravel project scaffolded with **Laravel Breeze**, including RESTful API endpoints for product management and basic authentication.

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 20
- npm
- MySQL
- Laravel 12.x (latest stable)

## Requirements
Credentials
- Email : admin@test.com
- Password : admin123
## Installation

```bash
git clone https://github.com/ayieikmal97/bestweb.git

cd bestweb

composer install

npm install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed

npm run dev

php artisan serve