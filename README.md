
## TODO Project

### Built With

* [![Laravel][Laravel.com]][Laravel-url]

## Introduction

This is a simple Laravel project that demonstrates the basic setup and usage of Laravel framework. It includes user
authentication(sanctum), database migrations, and a basic CRUD functionality.


### Requirements

* PHP >= 8.1
* laravel 11
* Composer
* sqlite

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/MR-EP97/karlancer-laravel-project.git
   
   cd karlancer-laravel-project
   ```
2. Install
   ```sh
   composer i
   ```
3. migrate and seed
   ```sh
   php artisan migrate
   
   php artisan db:seed
   ```
4. run
   ```sh
    php artisan serve --port=80
   
    php artisan queue:work
   
    php artisan route:clear
   ```
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
