<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Not implemented
    }

    /**
     * Show the current user's profile.
     */
    public function me(Request $request)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("User profile");

        // Get the user's profile in the MySQL database
        $user = \App\Models\User::where('id', $request->user()->id)->first();

        $output->writeln("User id: " . $user->id);

        // Get the user's rides in the MySQL database, get only the public rides
        $rides = [];
        $rides = $user->rides();

        $output->writeln("Rides: " . $rides->count());

        // Get the user's followers in Neo4j, use the User model method
        $followers = $user->followers();

        $output->writeln("Followers: " . $followers->count());

        // Get the user's following in Neo4j, use the User model method
        $following = $user->following();

        $output->writeln("Following: " . $following->count());

        // Return the user's profile, rides and followers
        return response()->json([
            'user' => $user,
            'rides' => $rides,
            'followers' => $followers,
            'following' => $following,
        ]);
    }

    // Get the user's following list
    public function following(Request $request)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("User following");

        // Get the user's profile in the MySQL database
        $user = \App\Models\User::where('id', $request->user()->id)->first();

        $output->writeln("User id: " . $user->id);

        // Get the user's following in Neo4j, use the User model method
        $following = $user->following();

        $output->writeln("Following: " . $following->count());

        // Return the user's following
        return response()->json([
            'following' => $following,
        ]);
    }

    // Get the user's followers list
    public function followers(Request $request)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("User followers");

        // Get the user's profile in the MySQL database
        $user = \App\Models\User::where('id', $request->user()->id)->first();

        $output->writeln("User id: " . $user->id);

        // Get the user's followers in Neo4j, use the User model method
        $followers = $user->followers();

        $output->writeln("Followers: " . $followers->count());

        // Return the user's followers
        return response()->json([
            'followers' => $followers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate and store the request...
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|confirmed|max:255',
        ]);

        $user = new \App\Models\User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'message' => 'User created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get the user's profile in the MySQL database
        $user = \App\Models\User::where('id', $id)->first();

        // Get the user's rides in the MySQL database, get only the public rides
        $rides = $user->rides()->where('public', true)->get();

        // Get the user's followers in Neo4j, use the User model method
        $followers = $user->followers();

        // Get the user's following in Neo4j, use the User model method
        $following = $user->following();

        // Return the user's profile, rides and followers
        return response()->json([
            'user' => $user,
            'rides' => $rides,
            'followers' => $followers,
            'following' => $following,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate and store the request...
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|unique:users|max:255',
            'password' => 'string|confirmed',
        ]);

        $user = \App\Models\User::where('id', $id)->first();

        if ($validated['name']) {
            $user->name = $validated['name'];
        }

        if ($validated['email']) {
            $user->email = $validated['email'];
        }

        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete the user's profile in the MySQL database
        $user = \App\Models\User::where('id', $id)->first();
        $user->delete();

        // If the user is deleted, return a Content Deleted response
        return response()->json([
            'message' => 'User deleted'
        ], 204);
    }

    public function searchUsers(Request $request)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("Search users");

        // if query parameter is not set, or is empty, return an error
        if (!$request->query('query')) {
            return response()->json([
                'message' => 'Query parameter is required'
            ], 400);
        }

        // Get and validate the query
        $query = $request->validate([
            'query' => 'required|string|max:255',
        ])['query'];

        $output->writeln("Query: " . $query);

        // Get the users from the MySQL database and paginate the results
        $users = \App\Models\User::where('name', 'LIKE', '%' . $query . '%')->paginate(10);

        // Remove current user from the results
        $users = $users->except($request->user()->id);

        $output->writeln("Users: " . $users->count());

        $currentUser = $request->user();

        $output->writeln("Current user: " . $currentUser->id);

        $following = $currentUser->following();

        $output->writeln("Following: " . $following->count());

        $searchResults = [];

        foreach ($users as $user) {
            $searchResults[] = [
                'user' => $user,
                'following' => $following->contains('id', $user->id),
            ];
        }

        // Return the users
        return response()->json($searchResults);
    }

    public function follow(Request $request, string $id)
    {
        // Save the follow relationship in Neo4j
        $neo4j = app('neo4j');

        // log the user id and the user id to follow
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("User id: " . $request->user()->id);
        $output->writeln("User id to follow: " . $id);

        $id = (int) $id;

        // log type of id and user id
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("Type of id: " . gettype($id));
        $output->writeln("User id: " . gettype($request->user()->id));

        $query = "
            MATCH (u:User), (u2:User)
            WHERE u.user_id = \$userId
            AND u2.user_id = \$userId2
            CREATE (u)-[:FOLLOWS]->(u2)";

        $parameters = [
            'userId' => $request->user()->id,
            'userId2' => $id,
        ];

        $result = $neo4j->writeTransaction(function ($tx) use ($query, $parameters) {
            return $tx->run($query, $parameters);
        });

        //$result = $neo4j->run($query, $parameters);

        $output->writeln("Query is done");

        $output->writeln("Result: " . $result->getSummary()->getCounters()->relationshipsCreated());

        return response()->json([
            'message' => 'Followed'
        ], 201);
    }

    public function unfollow(Request $request, string $id)
    {
        // Delete the follow relationship in Neo4j
        $neo4j = app('neo4j');

        // cast id to integer
        $id = (int) $id;

        // log type of id and user id
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("Type of id: " . gettype($id));
        $output->writeln("User id: " . gettype($request->user()->id));

        $query = "
            MATCH (u:User)-[f:FOLLOWS]->(u2:User)
            WHERE u.user_id = \$userId
            AND u2.user_id = \$userId2
            DELETE f";

        $parameters = [
            'userId' => $request->user()->id,
            'userId2' => $id,
        ];

        $result = $neo4j->writeTransaction(function ($tx) use ($query, $parameters) {
            return $tx->run($query, $parameters);
        });

        //$result = $neo4j->run($query, $parameters);

        $output->writeln("Query is done");

        $output->writeln("Result: " . $result->getSummary()->getCounters()->relationshipsDeleted());

        return response()->json([
            'message' => 'Unfollowed'
        ], 200);
    }
}
