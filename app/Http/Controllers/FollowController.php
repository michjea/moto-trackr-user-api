<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request, string $id)
    {
        // Save the follow relationship in Neo4j
        $neo4j = app('neo4j');

        $query = "
            MATCH (u:User), (u2:User)
            WHERE u.id = \$userId
            AND u2.id = \$userId2
            CREATE (u)-[:FOLLOWS]->(u2)";

        $parameters = [
            'userId' => $request->user()->id,
            'userId2' => $id,
        ];

        $neo4j->run($query, $parameters);

        return response()->json([
            'message' => 'Followed'
        ], 201);
    }

    public function unfollow(Request $request, string $id)
    {
        // Delete the follow relationship in Neo4j
        $neo4j = app('neo4j');

        $query = "
            MATCH (u:User)-[f:FOLLOWS]->(u2:User)
            WHERE u.id = \$userId
            AND u2.id = \$userId2
            DELETE f";

        $parameters = [
            'userId' => $request->user()->id,
            'userId2' => $id,
        ];

        $neo4j->run($query, $parameters);

        return response()->json([
            'message' => 'Unfollowed'
        ], 200);
    }
}
