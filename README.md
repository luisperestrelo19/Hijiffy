# Hijiffy Backend Challenge

## 📚 System Overview

This project is a Laravel microservice designed to manage availability reservations, with integration via webhook with a Dialogflow agent.

### 🧱 Architecture

- Laravel 12
- REST API
- Validation using FormRequests
- Response using JsonResource.
- Authentication via Laravel Sanctum tokens
- Dialogflow ES (Export included)
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

## 📄 Documentation

To simplify the implementation and maintenance of the API documentation, the project uses the Laravel package [Scramble](https://scramble.dedoc.co/). This tool automatically generates an interactive and developer-friendly interface for exploring all available API endpoints.

Once the server is running, the documentation can be accessed at:

🔗 http://localhost:8000/docs/api#/


There is a database structure file (database.mwb) located inside the database folder. This file is intended to be opened with MySQL Workbench.
## ▶️ How to run the project

### 📝 Step-by-step Setup

1. **Clone the repository**
    ```bash
    git clone https://github.com/luisperestrelo19/Hijiffy
    cd hijiffy-backend
    ```

2. **Install dependencies**
    ```bash
    composer install
    ```

3. **Copy environment file**
    ```bash
    cp .env.example .env
    ```

4. **Generate application key**
    ```bash
    php artisan key:generate
    ```

5. **Start Docker containers**
    ```bash
    cd docker
    docker compose up
    ```

6. **Run database migrations**
    ```bash
    php artisan migrate
    ```

7. **Serve the application**
    ```bash
    php artisan serve
    ```

## 🤖 Dialogflow Agent

- Exports included in the `dialogflow/` folder with .json files for each Intent
- Webhook: POST to `/api/webhook`


### 🌐 Testing Dialogflow Webhook with ngrok

To test the Dialogflow webhook locally during development, ngrok was used to expose the local Laravel server to the internet:

https://ngrok.com/

```bash
php artisan serve
ngrok http 8000
```

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
The data file to import should be placed in the same folder as the `ImportProperties` command. Make sure your JSON file is located alongside `app/Console/Commands/ImportProperties/ImportProperties.php` before running the command.

You can find the command implementation in `app/Console/Commands/ImportProperties/ImportProperties.php`.


### 📦 API responses

The API always returns a JSON object. It does not include a specific property indicating success or failure. I
nstead, the outcome of the request is determined by the HTTP status code.

### ❌ Example: Validation Error Response

When submitting invalid data to a form endpoint (e.g. `POST /api/register`), the API responds with an HTTP 422 Unprocessable Entity status and a JSON body like the following:

```json
{
  "message": "The name field is required. (and 2 more errors)",
  "errors": {
    "name": [
      "The name field is required."
    ],
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```
### ✅ Example: Successful Response

On a successful request (e.g. `POST /api/login`), the API returns a JSON object like this, along with a 200 OK status code:

```json
{
  "user": {
    "id": 1,
    "name": "Luís Perestrelo",
    "email": "luisperestrelo19@gmail.com"
  },
  "token": "8|ndhvf5iXmQwGOYijR84qP42Os0uHs5D3tsWxH1oR962e8de4"
}
```

## 🐳 Docker Setup

Navigate to the docker folder:

```bash
cd docker
```

Start containers:

```bash
docker compose up
```

The `.env.example` file includes credentials. After setup, run the project as usual.
