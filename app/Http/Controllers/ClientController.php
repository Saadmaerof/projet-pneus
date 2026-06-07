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
            'user'    => $user->load('client'),
            'token'   => $token,
        ], 200);
    }



    // Méthode pour récupérer la liste des clients avec filtrage
      public function index(Request $request)
      {
          $validated = $request->validate([
              'nom' => 'nullable|string|max:255',
              'statut' => 'nullable|string|in:actif,bloque,bloqué',
          ]);

          $statut = $validated['statut'] ?? null;
          if ($statut === 'bloqué') {
              $statut = 'bloque';
          }

          $query = User::with('client')->whereHas('client');

          if (!empty($validated['nom'])) {
              $search = '%' . trim($validated['nom']) . '%';
              $query->where(function ($q) use ($search) {
                  $q->where('nom', 'like', $search)
                    ->orWhere('prenom', 'like', $search);
              });
          }

          if (!empty($statut)) {
              $query->whereHas('client', function ($q) use ($statut) {
                  $q->where('statut', $statut);
              });
          }

          $clients = $query->get();

          return response()->json([
              'message' => 'Clients récupérés avec succès',
              'data' => $clients,
          ], 200);
      }


// Méthode pour changer le statut d'un client (actif/bloqué)
      public function changerStatut(Request $request, $id)
      {
          $validated = $request->validate([
              'statut' => 'required|string|in:actif,bloque,bloqué',
          ]);

          $statut = $validated['statut'];
          if ($statut === 'bloqué') {
              $statut = 'bloque';
          }

          $client = Client::find($id);
          if (!$client) {
              return response()->json(['message' => 'Client non trouvé.'], 404);
          }

          $client->statut = $statut;
          $client->save();

          return response()->json([
              'message' => 'Statut du client mis à jour avec succès',
              'data' => $client,
          ], 200);
      }
     
public function stats()
{
    $totalClients = Client::count();
    $clientsActifs = Client::where('statut', 'actif')->count();

    return response()->json([
        'message' => 'Statistiques des clients',
        'data' => [
            'total' => $totalClients,
            'actifs' => $clientsActifs,
            'bloques' => $totalClients - $clientsActifs,
        ],
    ], 200);
}







}