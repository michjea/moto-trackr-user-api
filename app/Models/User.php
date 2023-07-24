<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all of the rides for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    // Boot function
    protected static function boot()
    {
        parent::boot();

        // Create User in Neo4j
        static::created(function ($user) {
            // Log the user in console
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $output->writeln("Trying to create user " . $user->id . " in Neo4j");

            $neo4j = app('neo4j');

            $query = "
                CREATE (u:User {user_id: \$id})
                RETURN u";

            $parameters = [
                'id' => $user->id,
            ];

            $neo4j->run($query, $parameters);
        });

        // Delete User in Neo4j
        static::deleted(function ($user) {
            $neo4j = app('neo4j');

            $query = "
                MATCH (u:User {user_id: \$id})
                DETACH DELETE u";

            $parameters = [
                'id' => $user->id,
            ];

            $neo4j->run($query, $parameters);
        });
    }

    // Get the user's following list from Neo4j
    public function following()
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("Getting following for user " . $this->id);

        $neo4j = app('neo4j');

        $query = "
            MATCH (u:User)-[:FOLLOWS]->(u2:User)
            WHERE u.user_id = \$userId
            RETURN u2";

        $parameters = [
            'userId' => $this->id,
        ];

        $results = $neo4j->run($query, $parameters);

        $output->writeln("Results: " . $results->count());

        $following = [];

        $output->writeln("Test");

        // if empty following, return empty array
        if (empty($results)) {
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $output->writeln("Empty following");
            return [];
        }

        $output->writeln("Not empty following");

        foreach ($results as $result) {
            // result is a map. get the user_id from the map
            foreach ($result as $key => $value) {
                $output->writeln("Key: " . $key);
                $output->writeln("Value: " . $value->getProperty('user_id'));

                $following[] = $value->getProperty('user_id');
            }
        }

        // Get the users from the MySQL database
        $users = User::whereIn('id', $following)->get();

        $output->writeln("Users: " . $users->count());

        return $users;
    }

    // Get the user's followers list from Neo4j
    public function followers()
    {
        $neo4j = app('neo4j');

        $query = "
            MATCH (u:User)<-[:FOLLOWS]-(u2:User)
            WHERE u.user_id = \$userId
            RETURN u2";

        $parameters = [
            'userId' => $this->id,
        ];

        $results = $neo4j->run($query, $parameters);

        $followers = [];

        foreach ($results as $result) {
            // result is a map. get the user_id from the map
            foreach ($result as $key => $value) {
                $followers[] = $value->getProperty('user_id');
            }
        }

        // Get the users from the MySQL database
        $users = User::whereIn('id', $followers)->get();

        return $users;
    }
}
