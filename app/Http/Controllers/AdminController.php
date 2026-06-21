<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use App\Models\Admin;
use App\Models\Technicien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    // Méthode d'inscription pour les admins
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'prenom'      => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'=> 'required|string|min:6|confirmed', // password_confirmation requis
        ]);
         // Créer l'utilisateur
        $user = User::create([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'password' => Hash::make($request->password), //sécuriser le mot de passe
            'role'         => 'Admin',
        ]);

        // Créer le Admin associé à l'utilisateur
        $admin = Admin::create([
            'users_id' => $user->id,
        ]);

        // Générer un token d'authentification pour le client
         $token = $user->createToken('auth_token')->plainTextToken;

         // Retourner la réponse avec les données du client et le token
        return response()->json([
            'message' => 'Compte Admin créé avec succès',
            'data'    => $user->load('admin'),
            'token'   => $token,
        ], 201);
    }


    // Méthode de connexion pour les technicien
 public function ajouterTechnicien(Request $request)
{
    $request->validate([
        'nom'                 => 'required|string|max:255',
        'prenom'              => 'required|string|max:255',
        'email'               => 'required|email|unique:users,email',
        'password'            => 'required|string|min:6|confirmed', 
        'lignes'              => 'required|array|min:1',
        'lignes.*.service_id' => 'required|exists:services,id',
    ]);

    $user = User::create([
        'nom'      => $request->nom,
        'prenom'   => $request->prenom,
        'email'    => $request->email,
        'password' => Hash::make($request->password), 
        'role'     => 'Technicien',
    ]);

    $technicien = Technicien::create([
        'users_id'      => $user->id,
        'statut'        => 'actif', 
        'disponibilite' => true,
    ]);

    $technicien->ajouterService_Technicien($request->lignes);

    return response()->json([
        'message'        => 'Compte Technicien créé avec succès',
        'data'           => $user,
        'num_technicien' => 'TECH-' . str_pad($technicien->id, 3, '0', STR_PAD_LEFT), 
    ], 201);
}






    
}
