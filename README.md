# Moto Tracker User API

## Description

This is the user API for the Moto Tracker application.

## Installation and Setup Instructions for Local Development

Start Apache and MySQL servers using XAMPP.

Apply migrations using `php artisan migrate`.

Start server using `php artisan serve`.

Get routes using `php artisan route:list`.

## API Endpoints

### User

#### Register

`POST /api/register`

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
