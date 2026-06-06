<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Dom\Comment;
use Illuminate\Http\Request;

class CommandeController extends Controller
{

public function store(Request $request) {
    $user = $request->user();

    $request->validate([
        'lignes' => 'required|array|min:1',
        'lignes.*.pneu_id' => 'required|exists:pneus,id',
        'lignes.*.quantite' => 'required|integer|min:1',
    ]);
     

    $commande = Commande::create([
        'client_id' => $user->client->id,
        'statut' => 'en attente',

    ]);
    $commande->calculerMontantTotal($request->lignes);
    $commande->ajouterLigneCommande($request->lignes);


   





}
public function index() {
    $commandes = Commande::All();
 
    return response()->json($commandes);
}
public function updatestatut(Request $request) {
    $request->validate([
        'id' => 'required|exists:commandes,id',
        'statut' => 'required|string|in:en attente,validee,livree,annulee',
    ]);
    $commande = Commande::findOrFail($request->id);
    $commande->update(['statut' => $request->statut]);
    return response()->json(['message' => 'Commande créée avec succès',
            'commande_id' => $commande->id,
            'data'        => $commande,],201);
}


}