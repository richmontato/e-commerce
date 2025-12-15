# Laravel Docker Setup & Troubleshooting Guide

This guide documents the changes and steps taken to get the Laravel e-commerce project running in Docker, resolving the 500 error and ensuring a working local environment. Share this with your coworkers to help them set up from the original folder.

---

## 1. Created and Updated `.env` File

- Added a new `.env` file in the Laravel project root with the following key settings:
  - `APP_KEY` (generated via artisan)
  - Database credentials matching `docker-compose.yml`:
    - `DB_CONNECTION=mysql`
    - `DB_HOST=mysql`
    - `DB_PORT=3306`
    - `DB_DATABASE=laravel`
    - `DB_USERNAME=laravel`
    - `DB_PASSWORD=pmkitagachor`
  - Other required Laravel environment variables (see below for full example).

## 2. Generated Laravel Application Key

- Ran:
  ```sh
  docker exec -it laravel_app php artisan key:generate
  ```
- This sets the `APP_KEY` in `.env` and is required for Laravel to run.

## 3. Ran Database Migrations

- Ran:
  ```sh
  docker exec -it laravel_app php artisan migrate
  ```
- Ensures the database schema is up to date.

## 4. Cleared Laravel Caches

- Ran:
  ```sh
  docker exec -it laravel_app php artisan config:cache
  docker exec -it laravel_app php artisan cache:clear
  docker exec -it laravel_app php artisan config:clear
  docker exec -it laravel_app php artisan route:clear
  docker exec -it laravel_app php artisan view:clear
  docker exec -it laravel_app php artisan optimize:clear
  ```
- This ensures Laravel loads the latest environment and config.

## 5. Restarted the Laravel Container

- Ran:
  ```sh
  docker restart laravel_app
  ```
- Reloads the environment and config inside the container.

## 6. Started Laravel Development Server (if needed)

- If the app does not auto-start, run:
  ```sh
  docker exec -it laravel_app php artisan serve --host=0.0.0.0 --port=8000
  ```

---

## Example `.env` File

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=pmkitagachor

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Summary of Changes

- Added and configured `.env` file
- Synced DB credentials with Docker Compose
- Generated `APP_KEY`
- Ran migrations
- Cleared all caches
- Restarted container
- Ensured server is running on port 8000

---

**Share this guide with your team to replicate the working setup.**
