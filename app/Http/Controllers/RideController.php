<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ride;

class RideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rides = Ride::all();

        return response()->json($rides);
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
        // log the request
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("Request store ride");

        // Validate and store the request...
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'public' => 'boolean',
            'distance' => 'numeric',
            'duration' => 'numeric',
            'max_speed' => 'numeric',
            'avg_speed' => 'numeric',
            'positions' => 'array',
        ]);

        $output->writeln("Validated");

        $ride = new Ride();
        $ride->title = $validated['title'];
        $ride->description = $validated['description'];
        $ride->public = $validated['public'];
        $ride->distance = $validated['distance'];
        $ride->duration = $validated['duration'];
        $ride->max_speed = $validated['max_speed'];
        $ride->avg_speed = $validated['avg_speed'];
        $ride->positions = $validated['positions'];
        $ride->user_id = $request->user()->id;

        $ride->save();

        $output->writeln("Ride created: " . $ride);

        return response()->json([
            'message' => 'Ride created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ride = Ride::find($id);

        return response()->json($ride);
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
        // Validate and update the request...
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'public' => 'boolean',
            'distance' => 'numeric',
            'duration' => 'numeric',
            'max_speed' => 'numeric',
            'avg_speed' => 'numeric',
            'positions' => 'array',
        ]);

        $ride = Ride::find($id);
        $ride->title = $validated['title'];
        $ride->description = $validated['description'];
        $ride->public = $validated['public'];
        $ride->distance = $validated['distance'];
        $ride->duration = $validated['duration'];
        $ride->max_speed = $validated['max_speed'];
        $ride->avg_speed = $validated['avg_speed'];
        $ride->positions = $validated['positions'];
        $ride->user_id = $request->user()->id;

        $ride->save();

        return response()->json([
            'message' => 'Ride updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Remove the resource from storage...
        $ride = Ride::find($id);
        $ride->delete();

        return response()->json([
            'message' => 'Ride deleted'
        ], 200);
    }
}
