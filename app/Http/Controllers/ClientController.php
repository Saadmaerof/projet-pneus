<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class ClientController extends Controller
{
    // Méthode d'inscription pour les clients
public function register(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'prenom'      => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'=> 'required|string|min:6|confirmed', // password_confirmation requis
            'telephone'   => 'required|string|max:20',
            'adresse'     => 'required|string|max:255',
        ]);
         // Créer l'utilisateur
        $user = User::create([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'password' => Hash::make($request->password), //sécuriser le mot de passe
            'role'         => 'client',
        ]);

        // Créer le client associé à l'utilisateur
        $client = Client::create([
            'telephone'      => $request->telephone,
            'adresse'        => $request->adresse,
            'statut'         => 'actif',
            'user_id' => $user->id,
        ]);

        // Générer un token d'authentification pour le client
         $token = $user->createToken('auth_token')->plainTextToken;

         // Retourner la réponse avec les données du client et le token
        return response()->json([
            'message' => 'Compte client créé avec succès',
            'data'    => $user->load('client'),
            'token'   => $token,
        ], 201);
    }
    
     
    // Méthode de connexion pour les clients
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


// Récupérer l'utilisateur authentifié
 $user =User::where('email', $request->email)->firstOrFail();

// Révoquer tous les tokens d'authentification existants pour l'utilisateur
 $user->tokens()->delete();

// Générer un token d'authentification pour le client
$token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Connexion réussie',
            'data'    => $user->load('client'),
            'token'   => $token,
        ], 200);
    }


public function logout(Request $request)
     {
    // Révoquer tous les tokens d'authentification de l'utilisateur
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'Déconnexion réussie'], 200);


      }
      public function index()
      {
          $clients = User::with('client')->get();
          return response()->json($clients);
      }

}