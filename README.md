# Moto Trackr User API

## Description

This is the user API for the Moto Trackr application.

## Installation and Setup Instructions for Local Development

1. Clone this repository.

<<<<<<< HEAD
2. Start Apache and MySQL servers using XAMPP.
=======
Composer install using `composer install`.

Generate application key using `php artisan key:generate`.

<<<<<<< HEAD
Apply migrations using `php artisan migrate`.
>>>>>>> c34fabb (changed deploy pipeline to match new route)
=======
Apply migrations using `php artisan migrate`. 2. Start Apache and MySQL servers using XAMPP.
>>>>>>> 1023089 (anged deploy pipeline to match new route)

3. Generate key using `php artisan key:generate`.

4. Apply migrations using `php artisan migrate`.

5. Start server using `php artisan serve`.

-   Get routes using `php artisan route:list`.

## API Endpoints

### User

#### Register

`POST /api/auth/register`

##### Request

```json
{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "mysupersecurepassword",
    "password_confirmation": "mysupersecurepassword",
    "device_name": "Postman"
}
```

##### Response

```json
{
    "user": {
        "name": "John Doe",
        "email": "johndoe@example.com",
        "updated_at": "2023-06-02T17:28:42.000000Z",
        "created_at": "2023-06-02T17:28:42.000000Z",
        "id": 1
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

#### Login

`POST /api/auth/login`

##### Request

```json
{
    "email": "johndoe@example.com",
    "password": "mysupersecurepassword",
    "device_name": "Postman"
}
```

##### Response

```json
{
    "user": {
        "name": "John Doe",
        "email": "johndoe@example.com",
        "updated_at": "2023-06-02T17:28:42.000000Z",
        "created_at": "2023-06-02T17:28:42.000000Z",
        "id": 1
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```
