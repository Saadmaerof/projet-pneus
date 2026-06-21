<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Dom\Comment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommandeController extends Controller
{

/*public function index(Request $request)
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
}*/
public function index(Request $request)
{
    $validated = $request->validate([
        'date'   => 'nullable|date',
        'statut' => 'nullable|string|in:en attente,validée,livrée,annulée',
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

    // Transformation pour renvoyer uniquement les champs de la table "Commandes"
    $customData = $commandes->map(function ($commande) {
        return [
            'id'       => $commande->id,
            'num_cmd'  => 'CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT), // Ex: CMD-001
            'client'   => ($commande->client && $commande->client->user) 
                            ? trim(ucwords($commande->client->user->prenom . ' ' . $commande->client->user->nom)) 
                            : null,
            'date'     => \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y'), // Ex: 01/06/2025
            'montant'  => $commande->montant_total . ' MAD',
            'num_rdv'  => $commande->rendezvous 
                            ? 'RDV-' . str_pad($commande->rendezvous->id, 3, '0', STR_PAD_LEFT) 
                            : '—', // Affiche "—" s'il n'y a pas de RDV lié
            'statut'   => ucfirst($commande->statut), // Ex: Livré, Validé
        ];
    });

    return response()->json([
        'message' => 'Commandes récupérées avec succès',
        'data'    => $customData,
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
    $commande->freshstock($request->lignes);


   





}





public function updatestatut(Request $request) {
    $request->validate([
        'id' => 'required|exists:commandes,id',
        'statut' => 'required|string',
    ]);

    $commande = Commande::findOrFail($request->id);

    if ($commande->statut === 'livrée') {
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

/*public function show($id)
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
}*/
public function show($id)
{
    // Chargement de la commande avec ses relations (Note: utilise le bon nom 'lignesCommande' ou 'lignes_commande')
    $commande = Commande::with(['client.user', 'lignesCommande.pneu', 'rendezvous'])
        ->find($id);

    if (!$commande) {
        return response()->json([
            'success' => false,
            'message' => 'Commande non trouvée.'
        ], 404);
    }

    // les donnees necessaires 
    $customData = [
        'id'    => $commande->id,
        'num_cmd'     => 'CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT), // Ex: CMD-001
        'statut_commande'  => ucfirst($commande->statut), // Ex: Livré
        'client'           => ($commande->client && $commande->client->user) 
                                ? trim(ucwords($commande->client->user->prenom . ' ' . $commande->client->user->nom)) 
                                : null,
        'date_commande'    => \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y'), // Ex: 01/06/2025
        'rendezvous_associe'=> $commande->rendezvous 
                                ? 'RDV-' . str_pad($commande->rendezvous->id, 3, '0', STR_PAD_LEFT) 
                                : 'Aucun rendez-vous associé',
        
        // Tableau des articles / pneus en bas
        'lignes_commande'  => $commande->lignesCommande->map(function ($ligne, $index) {
            return [
                'numero'        => $index + 1,
                'pneu'          => $ligne->pneu 
                                    ? $ligne->pneu->marque . ' ' . $ligne->pneu->modele 
                                    : 'Pneu inconnu', // Ex: Michelin Primacy 4
                'dimensions'    => $ligne->pneu 
                                    ? $ligne->pneu->largeur . '/' . $ligne->pneu->hauteur . ' R' . $ligne->pneu->diametre_pouces 
                                    : '—', // Ex: 205/55 R16
                'quantite'      => $ligne->quantite,
                'prix_unitaire' => $ligne->prix_unitaire . ' MAD',
                'sous_total'    => ($ligne->quantite * $ligne->prix_unitaire) . ' MAD',
            ];
        }),
        'montant_total'    => $commande->montant_total . ' MAD'
    ];

    return response()->json([
        'message' => 'Commande récupérée avec succès',
        'data'    => $customData,
    ], 200);
}



public function stats()
{
    $totalCommandes = Commande::count();
    $commandesEnAttente = Commande::where('statut', 'en attente')->count();

    return response()->json([
        'message' => 'Statistiques des commandes',
        'data' => [
            'total' => $totalCommandes,
            'en_attente' => $commandesEnAttente,
        ],
    ], 200);
}

// Méthode pour récupérer les dernières commandes
public function dernieresCommandes()
{

    $commandes = Commande::with('client')
        ->latest()
        ->take(8)
        ->get();

    // Transformation pour correspondre exactement à l'affichage de ta maquette
    $customData = $commandes->map(function ($commande) {
        return [
            'id'            => $commande->id,
            'num_cmd'       => 'CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT), // Ex: CMD-001
            'nom_client'    => $commande->client 
                                ? trim(ucwords($commande->client->user->prenom . ' ' . $commande->client->user->nom)) 
                                : 'Client inconnu', // Ex: Karim Benali
            'date'          => $commande->created_at ? $commande->created_at->format('d/m/Y') : null, // Ex: 01/06/2025
            'montant'       => $commande->montant_total, // Ex: 3060 (le "MAD" est géré côté front ou concaténé ici)
            'statut'        => $commande->statut,  // Ex: En attente, Validé, Livré, Annulé
        ];
    });

    return response()->json([
        'message' => 'Dernières commandes récupérées avec succès',
        'data'    => $customData,
    ], 200);
}


public function historiquecommandes(Request $request)
{
    // 1. Récupérer le client (via la relation ou l'utilisateur connecté standard $request->user())
    $client = $request->user()->client;

    if (!$client) {
        return response()->json(['message' => 'Client non trouvé'], 404);
    }

    // 2. Eager Loading (Chargement précoce) pour éviter les requêtes N+1
    $commandes = $client->commandes()
        ->with(['lignesCommande.pneu']) // Charge tout en seulement 3 requêtes SQL
        ->orderBy('date_commande', 'desc') // Optionnel mais recommandé pour un historique
        ->get();

    // 3. Transformation des données
    $customData = $commandes->map(function ($commande) {
        return [
            'id'              => $commande->id,
            'num_cmd'         => 'CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT),
            'date'            => Carbon::parse($commande->date_commande)->format('d/m/Y'),
            'statut'          => ucfirst($commande->statut),
            'lignes_commande' => $commande->lignesCommande->map(function ($ligne, $index) {
                $pneu = $ligne->pneu; // On stocke pour éviter de répéter l'appel

                return [
                    'numero'        => $index + 1,
                    'pneu'          => $pneu ? "{$pneu->marque} {$pneu->modele}" : 'Pneu inconnu',
                    'dimensions'    => $pneu ? "{$pneu->largeur}/{$pneu->hauteur} R{$pneu->diametre_pouces}" : '—',
                    'quantite'      => $ligne->quantite,
                    'prix_unitaire' => $ligne->prix_unitaire . ' MAD',
                    'sous_total'    => ($ligne->quantite * $ligne->prix_unitaire) . ' MAD',
                ];
            }),
            'montant_total'   => $commande->montant_total . ' MAD'
        ];
    });

    // 4. Ne pas oublier de retourner la réponse !
    return response()->json($customData);
}

}