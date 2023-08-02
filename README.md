# Moto Trackr User API

## Description

This is the user API for the Moto Trackr application.

## Documentation

The documentation for this API can be found in /public/docs/index.html.

## Installation for Bachelor's Thesis

1.  Run Docker Compose

        ```bash
        docker-compose up -d
        ```

2.  Access the API at `http://localhost:8080`

## Installation and Setup Instructions for Local Development

1. Clone this repository.

2. Install dependencies using `composer install`.

3. Copy `.env.example` to `.env`.

4. Start Apache and MySQL servers using XAMPP.

    - Or start Docker mysql image using `docker run --name mysql -e MYSQL_ROOT_PASSWORD=secret -d mysql:latest`
    - Start Docker neo4j image `docker run --name neo4j -p 7474:7474 -p 7687:7687 -d -e NEO4J_AUTH=neo4j/secret neo4j:latest`

5. Generate key using `php artisan key:generate`.

6. Apply migrations using `php artisan migrate`.

7. Start server using `php artisan serve`.

-   Get routes using `php artisan route:list`.

8. Start tests using `php artisan test`.

9. Generate documentation using `php artisan scribe:generate`.
