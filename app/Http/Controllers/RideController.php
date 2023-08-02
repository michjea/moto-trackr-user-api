<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ride;
use Illuminate\Support\Facades\Validator;

/**
 * @authenticated
 * @group Rides
 *
 * APIs pour gérer les trajets
 */
class RideController extends Controller
{
    /**
     * @authenticated
     * Récupérer tous les trajets
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
     * @authenticated
     * Store a newly created resource in storage.
     * @bodyParam title string required Le titre du trajet
     * @bodyParam description string La description du trajet
     * @bodyParam public boolean Le trajet est-il public ?
     * @bodyParam distance numeric La distance du trajet
     * @bodyParam duration numeric La durée du trajet
     * @bodyParam max_speed numeric La vitesse maximale du trajet
     * @bodyParam avg_speed numeric La vitesse moyenne du trajet
     * @bodyParam positions array Les positions du trajet
     * @bodyParam route array La route du trajet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'public' => 'boolean',
            'distance' => 'numeric',
            'duration' => 'numeric',
            'max_speed' => 'numeric',
            'avg_speed' => 'numeric',
            'positions' => 'array',
            'route' => 'array',
        ]);

        // Si la validation échoue, renvoyer une réponse JSON avec les erreurs de validation
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Créer un nouveau trajet avec les données validées et l'associer à l'utilisateur
        $ride = $request->user()->rides()->create($validator->validated());

        return response()->json([
            'message' => 'Ride created',
            'ride' => $ride
        ], 201);
    }

    /**
     * Display the specified resource.
     * @urlParam id string required L'identifiant du trajet
     */
    public function show(Request $request, string $id)
    {
        $ride = Ride::findOrfail($id);

        if ($ride->public || $ride->user_id == $request->user()->id) {
            return response()->json($ride);
        } else {
            return response()->json([
                'message' => 'You are not allowed to access this resource'
            ], 401);
        }
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
     *
     * @urlParam id string required L'identifiant du trajet
     * @bodyParam title string required Le titre du trajet
     * @bodyParam description string La description du trajet
     * @bodyParam public boolean Le trajet est-il public ?
     * @bodyParam distance numeric La distance du trajet
     * @bodyParam duration numeric La durée du trajet
     * @bodyParam max_speed numeric La vitesse maximale du trajet
     * @bodyParam avg_speed numeric La vitesse moyenne du trajet
     * @bodyParam positions array Les positions du trajet
     * @bodyParam route array La route du trajet
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'public' => 'boolean',
            'distance' => 'numeric',
            'duration' => 'numeric',
            'max_speed' => 'numeric',
            'avg_speed' => 'numeric',
            'positions' => 'array',
            'route' => 'array',
        ]);

        // Si la validation échoue, renvoyer une réponse JSON avec les erreurs de validation
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ride = Ride::find($id);

        if ($ride->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to access this resource'
            ], 401);
        }


        $ride->update($validator->validated());

        return response()->json([
            'message' => 'Ride updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @urlParam id string required L'identifiant du trajet
     */
    public function destroy(Request $request, string $id)
    {
        // Remove the resource from storage...
        $ride = Ride::findOrfail($id);

        if ($ride->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to access this resource'
            ], 401);
        }

        $ride->delete();

        return response()->json([
            'message' => 'Ride deleted'
        ], 200);
    }
}
