<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Dom\Comment;
use Illuminate\Http\Request;

class CommandeController extends Controller
{

public function index(Request $request)
{
    $validated = $request->validate([
        'date' => 'nullable|date',
        'statut' => 'nullable|string|in:en attente,validee,livree,annulee',
        'client' => 'nullable|string|max:255',
    ]);

    $query = Commande::with(['client.user', 'rendezvous']);

    if (!empty($validated['date'])) {
        $query->whereDate('date_commande', $validated['date']);
    }

    if (!empty($validated['statut'])) {
        $query->where('statut', $validated['statut']);
    }

    if (!empty($validated['client'])) {
        $clientSearch = trim($validated['client']);
        $parts = preg_split('/\s+/', $clientSearch, -1, PREG_SPLIT_NO_EMPTY);

        $query->whereHas('client.user', function ($q) use ($parts, $clientSearch) {
            if (count($parts) === 1) {
                $q->where('nom', 'LIKE', '%' . $clientSearch . '%')
                  ->orWhere('prenom', 'LIKE', '%' . $clientSearch . '%');
            } else {
                $q->where(function ($sub) use ($parts) {
                    $sub->where(function ($item) use ($parts) {
                        $item->where('nom', 'LIKE', '%' . $parts[0] . '%')
                             ->where('prenom', 'LIKE', '%' . $parts[1] . '%');
                    })
                    ->orWhere(function ($item) use ($parts) {
                        $item->where('nom', 'LIKE', '%' . $parts[1] . '%')
                             ->where('prenom', 'LIKE', '%' . $parts[0] . '%');
                    });
                });
            }
        });
    }

    $commandes = $query->get();

    return response()->json([
        'message' => 'Commandes récupérées avec succès',
        'data' => $commandes,
    ], 200);
}

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





public function updatestatut(Request $request) {
    $request->validate([
        'id' => 'required|exists:commandes,id',
        'statut' => 'required|string|in:en attente,validee,livree,annulee',
    ]);

    $commande = Commande::findOrFail($request->id);

    if ($commande->statut === 'livree') {
        return response()->json([
            'success' => false,
            'message' => 'Le statut ne peut pas être modifié car la commande est déjà livrée.',
        ], 400);
    }

    $commande->update(['statut' => $request->statut]);
    return response()->json(['message' => 'Statut de la commande mis à jour avec succès',
            'commande_id' => $commande->id,
            'data'        => $commande,],201);
}

public function show($id)
{
    $commande = Commande::with(['client.user', 'lignesCommande.pneu', 'rendezvous'])
        ->find($id);

    if (!$commande) {
        return response()->json([
            'success' => false,
            'message' => 'Commande non trouvée.'
        ], 404);
    }

    return response()->json([
        'message' => 'Commande récupérée avec succès',
        'data' => $commande,
    ], 200);
}

}