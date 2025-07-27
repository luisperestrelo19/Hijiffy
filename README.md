# Hijiffy Backend Challenge

## 📚 System Overview

This project is a Laravel microservice designed to manage availability reservations, with integration via webhook with a Dialogflow agent.

### 🧱 Architecture

- Laravel 12
- REST API
- Dialogflow ES (Export included)
- Authentication via Laravel Sanctum tokens
- Validation using FormRequests
- Rate limiting

## 🚀 Available Endpoints

| Method     | Endpoint            | Controller                        | Description                                |
|------------|---------------------|------------------------------------|--------------------------------------------|
| POST       | /api/availabilities | AvailablityController@store        | Create new availability                    |
| GET        | /api/availabilities | AvailablityController@index        | List availabilities                        |
| POST       | /api/login          | AuthController@login               | Authenticate user                          |
| POST       | /api/logout         | AuthController@logout              | Logout user                                |
| POST       | /api/register       | RegisterController@register        | Register new user                          |
| POST       | /api/webhook        | DialogflowController@handleWebhook | Receive requests from Dialogflow           |


## 🗄️ Database Configuration

You can use MySQL database, or for quick setup and testing, SQLite is recommended.

To use SQLite:

1. Set your `.env` file to use SQLite:
    ```
    DB_CONNECTION=sqlite
    ```

2. If the `database/database.sqlite` file does not exist, running `php artisan migrate` will prompt you to create it:
    ```
    WARN  The SQLite database configured for this application does not exist: database/database.sqlite.
    ```

    Type `yes` when prompted, and Laravel will create the file automatically.

Run migrations after configuring your database:
```bash
php artisan migrate
```

## ▶️ How to run the project

```bash
git clone https://github.com/luisperestrelo19/Hijiffy
cd hijiffy-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 🤖 Dialogflow Agent

- Export included in the `dialogflow/` folder with .json files for each Intent
- Webhook: POST to `/api/webhook`

## 🧪 Tests

```bash
php artisan test
```

## 🚦 Rate Limiting & Caching

### Configuration
```php
'rate_limits' => [
    'api' => [
        'limit' => env('HIIJIFFY_RATE_LIMIT_API', 100),
    ],
    'sync-endpoint' => [
        'limit' => env('HIIJIFFY_RATE_LIMIT_SYNC', 10),
    ],
    'guest' => [
        'limit' => env('HIIJIFFY_RATE_LIMIT_GUEST', 5),
    ],
],
```

### RateLimiter Middleware
```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(config('hijiffy.rate_limits.api.limit'))
        ->by($request->user()->id);
});
```
Limits the number of requests per minute for authenticated users based on user ID, 100 is the base value.

```php
RateLimiter::for('sync', function (Request $request) {
    return Limit::perMinute(config('hijiffy.rate_limits.sync-endpoint.limit'))
        ->by($request->user()->id);
});
```
Applies to specific sensitive sync endpoints, also keyed by user ID. The default value is set to 10 because these requests require more processing than others.

```php
RateLimiter::for('guest', function (Request $request) {
    return Limit::perMinute(config('hijiffy.rate_limits.guest.limit'))
        ->by($request->ip());
});
```
Limits requests from unauthenticated users based on their IP address. The default limit is set to 5, as this rate limiter is primarily used for registration and authentication endpoints. This helps prevent abuse and protects the system from malicious actors.

## 📥 ImportProperties Command

A custom Artisan command is available to import properties data from a JSON file.

### Usage
```bash
php artisan hijiffy:import-properties
```
The data file to import should be placed in the same folder as the `ImportProperties` command. Make sure your JSON file is located alongside `app/Console/Commands/ImportProperties.php` before running the command.

You can find the command implementation in `app/Console/Commands/ImportProperties.php`.