<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request...
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->uncompromised(), 'max:255'],
            'device_name' => 'required|string|max:255'
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        $token = $user->createToken($validated['device_name'])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

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
