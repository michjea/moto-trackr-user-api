<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    // Fields
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'public',
        'created_at',
        'updated_at',
        'distance',
        'duration',
        'max_speed',
        'avg_speed',
        'positions',
        'route'
    ];

    // Casts
    protected $casts = [
        'positions' => 'array',
        'route' => 'array'
    ];

    // User can create many rides
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Boot function
    protected static function boot()
    {
        parent::boot();

        // Create Ride in Neo4j
        static::created(function ($ride) {
            $user = $ride->user;

            $query = "
                MATCH (u:User {user_id: \$userId})
                CREATE (u)-[:POSTED]->(r:Ride \$ride)
                RETURN r";

            $parameters = [
                'userId' => $user->id,
                'ride' => [
                    'ride_id' => $ride->id,
                    'public' => $ride->public,
                ],
            ];

            $neo4j = app('neo4j');

            $neo4j->run($query, $parameters);
        });

        // Update Ride in Neo4j
        static::updated(function ($ride) {
            $user = $ride->user;

            $query = "
                MATCH (u:User {user_id: \$userId})-[:POSTED]->(r:Ride {ride_id: \$rideId})
                SET r.public = \$public
                RETURN r";

            $parameters = [
                'userId' => $user->id,
                'rideId' => $ride->id,
                'public' => $ride->public,
            ];

            $neo4j = app('neo4j');

            $neo4j->run($query, $parameters);
        });

        // Delete Ride in Neo4j
        static::deleted(function ($ride) {
            $user = $ride->user;

            $query = "
                MATCH (u:User {user_id: \$userId})-[:POSTED]->(r:Ride {ride_id: \$rideId})
                DETACH DELETE r";

            $parameters = [
                'userId' => $user->id,
                'rideId' => $ride->id,
            ];

            $neo4j = app('neo4j');

            $neo4j->run($query, $parameters);
        });
    }
}
