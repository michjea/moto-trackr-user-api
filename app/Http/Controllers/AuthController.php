<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

/**
 * @unauthenticated
 * @group Authentification
 *
 * APIs pour l'authentification et l'enregistrement des utilisateurs
 */
class AuthController extends Controller
{
    /**
     * @unauthenticated
     * Enregistrer un nouvel utilisateur
     *
     * @bodyParam name string required Le nom de l'utilisateur
     * @bodyParam email string required L'adresse email de l'utilisateur
     * @bodyParam password string required Le mot de passe de l'utilisateur
     * @bodyParam password_confirmation string required La confirmation du mot de passe de l'utilisateur
     * @bodyParam device_name string required Le nom du périphérique de l'utilisateur
     */
    public function register(Request $request)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("User is registering");

        // Validate the request...
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->uncompromised(), 'max:255'],
            'device_name' => 'required|string|max:255'
        ]);


        $output->writeln("User " . $validated['name'] . " is registering");

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        $output->writeln("User " . $validated['name'] . " has been registered");

        $token = $user->createToken($validated['device_name'])->plainTextToken;

        $output->writeln("User " . $validated['name'] . " has a new token " . $token);

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * @unauthenticated
     * Connecter un utilisateur
     *
     * @bodyParam email string required L'adresse email de l'utilisateur
     * @bodyParam password string required Le mot de passe de l'utilisateur
     * @bodyParam device_name string required Le nom du périphérique de l'utilisateur
     */
    public function token(Request $request) // login function
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        // Vérifier si l'utilisateur a déjà un jeton
        if ($user->currentAccessToken()) {
            // Retourner le jeton existant

            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $output->writeln("User already has a token " . $user->currentAccessToken()->plainTextToken);

            return response()->json([
                'user' => $user,
                'token' => $user->currentAccessToken()->plainTextToken
            ], 200);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("User has a new token " . $token);

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
