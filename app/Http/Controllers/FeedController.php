<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeedController extends Controller
{
    //
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        $limit = 10;
        $offset = $request->input('offset', 0);
        // cast the offset to an integer
        $offset = (int) $offset;

        $neo4j = app('neo4j');

        // Get the posts from the user's following list. The user has a user_id field. The public field of the Ride node must be true.
        $query = "
            MATCH (u:User)-[:FOLLOWS]->(u2:User)-[:POSTED]->(r:Ride)
            WHERE u.id = \$userId
            AND r.public = true
            RETURN r
            ORDER BY r.created_at DESC
            SKIP \$offset
            LIMIT \$limit";

        $parameters = [
            'userId' => $userId,
            'offset' => $offset,
            'limit' => $limit,
        ];

        $results = $neo4j->run($query, $parameters);

        // get each post's id ride_id and get it from the MySQL database
        $rides = [];

        foreach ($results as $record) {
            foreach ($record as $key => $value) {
                $rideId = $value->getProperty('ride_id');
                $rides[] = $rideId;
            }
        }

        // if no rides, return empty array with message
        if (empty($rides)) {
            return response()->json([
                'message' => 'You are up to date. Follow more users to see their posts.',
            ], 200);
        }

        $posts = \App\Models\Ride::whereIn('ride_id', $rides)->get();

        return response()->json($posts);
    }
}
