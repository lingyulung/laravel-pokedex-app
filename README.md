# Laravel-Pokedex-App

This project contains api routes used to display Pokemon data for a Pokedex App.

## Getting Started

This project requires a php version of "^8.2"

Install the project dependencies:

```bash
composer install
```

Be sure to create an .env file with everything in .env.example and change CACHE_STORE from "database" to "file".

You also need put the APP_KEY into .env. You may get it from the project owner.

Run the following steps:

```bash
# Create the SQLite database file
touch database/database.sqlite

# Run migrations to create all required tables
php artisan migrate
```

Then serve the project files so that the frontend/client may make requests to it for the data:

```bash
php artisan serve
```
