<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Authentication\Authenticate;

class Neo4jServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register laudis/neo4j-php-client to the container
        $this->app->singleton('neo4j', function ($app) {
            $user = config('database.connections.neo4j.username');
            $password = config('database.connections.neo4j.password');
            $uri = config('database.connections.neo4j.uri');
            $driver = config('database.connections.neo4j.driver');

            $neo4j = ClientBuilder::create()
                ->withDriver($driver, $uri, Authenticate::basic($user, $password))
                ->build();

            return $neo4j;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
