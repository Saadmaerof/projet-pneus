<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller{

public function logout(Request $request)
     {
    // Révoquer tous les tokens d'authentification de l'utilisateur
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'Déconnexion réussie'], 200);


      }

      // Méthode de connexion pour les users
  public function login(Request $request)
    {
        // Valider les données de connexion
        $request->validate([
            'email'       => 'required|email',
            'password'=> 'required|string',
        ]);

       
     // Vérifier les informations d'identification
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            // Si les informations d'identification sont invalides, retourner une réponse d'erreur
    return response()->json(['message' => 'invalid email or password'], 401);
}



 $user =User::where('email', $request->email)->firstOrFail();
 $role = $user->role;


 $user->tokens()->delete();


$token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Connexion réussie',
            'user'    => $user->load($role),
            'token'   => $token,
        ], 200);
    }






}