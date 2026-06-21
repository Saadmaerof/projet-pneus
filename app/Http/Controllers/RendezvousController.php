<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Rendezvous;
use App\Models\LigneRendezvous;

class RendezvousController extends Controller
{
    //index : filtrer les rendezvous par date, statut et technicien (si statut = validé)
  /* public function index(Request $request)
{
    $validated = $request->validate([
        'date'           => 'nullable|date',
        'statut'         => 'nullable|string|in:en attente,validé,en cours,terminé,annulé',
        'technicien_nom' => 'nullable|string|max:255',
    ]);

    // On prépare la requête de base avec ses relations
    $query = Rendezvous::with(['client.user', 'vehicule', 'techniciens.user', 'commande']);

    // Filtre 1 : Par Date
    if (!empty($validated['date'])) {
        $query->whereDate('date', $validated['date']);
    }

    // Filtre 2 : Par Statut (Indépendant et simplifié)
    if (!empty($validated['statut'])) {
        $query->where('statut', $validated['statut']);
    }

    // Filtre 3 : Par Technicien (Nettoyé des conditions restrictives 'in_array')
    if (!empty($validated['technicien_nom'])) {
        $search = '%' . $validated['technicien_nom'] . '%';

        $query->whereHas('techniciens.user', function ($q) use ($search) {
            // Groupement logique des 'like' pour éviter que le orWhere ne casse le filtre global
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('nom', 'like', $search)
                         ->orWhere('prenom', 'like', $search);
            });
        });
    }

    $rendezvous = $query->get();

    return response()->json([
        'message' => $rendezvous->isEmpty()
            ? 'Aucun rendez-vous trouvé.'
            : 'Rendez-vous récupérés avec succès',
        'data' => $rendezvous,
    ], 200);
}*/
public function index(Request $request)
{
    $validated = $request->validate([
        'date'           => 'nullable|date',
        'statut'         => 'nullable|string|in:en attente,validé,en cours,terminé,annulé',
        'technicien_nom' => 'nullable|string|max:255',
    ]);

    $query = Rendezvous::with(['client.user', 'vehicule', 'techniciens.user', 'commande']);

    if (!empty($validated['date'])) {
        $query->whereDate('date', $validated['date']);
    }

    if (!empty($validated['statut'])) {
        $query->where('statut', $validated['statut']);
    }

    if (!empty($validated['technicien_nom'])) {
        $search = '%' . $validated['technicien_nom'] . '%';
        $query->whereHas('techniciens.user', function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('nom', 'like', $search)
                         ->orWhere('prenom', 'like', $search);
            });
        });
    }

    $rendezvous = $query->get();

    // Transformation pour ne garder QUE les champs visibles sur l'image
    $customData = $rendezvous->map(function ($item) {
        return [
            'id'          => $item->id,
            'num_rdv'     => 'RDV-' . str_pad($item->id, 3, '0', STR_PAD_LEFT), // Ex: RDV-001
            'client'       => ($item->client && $item->client->user) 
                                ? trim($item->client->user->prenom . ' ' . $item->client->user->nom) 
                                : null,
            'date_heure'   => $item->date . ' ' . substr($item->heure, 0, 5), // Exemple: 2025-06-05 09:00
            'categorie'    => $item->vehicule ? $item->vehicule->vehicule : null, // "Tourisme" ou "SUV / 4x4"
            'num_cmd' =>    $item->commande 
                            ? 'CMD-' . str_pad($item->commande->id, 3, '0', STR_PAD_LEFT) 
                            : '—', // Affiche "CMD-001" ou "—" si aucune commande associée
            'tarif_total'  => $item->tarifTotal ? $item->tarifTotal . ' MAD' : 'pas encore calculé', // Affiche le tarif total ou un message si non calculé
            'statut'       => ucfirst($item->statut), // "Terminé" ou "En cours"
        ];
    });

    return response()->json([
        'message' => $customData->isEmpty() ? 'Aucun rendez-vous trouvé.' : 'Rendez-vous récupérés avec succès',
        'data'    => $customData,
    ], 200);
}



    /*public function show($id)
    {
        $rendezvous = Rendezvous::with([
            'client.user',
            'vehicule',
            'commande',
            'ligne_rendezvous.service',
            'ligne_rendezvous.technicien.user',
        ])->find($id);

        if (!$rendezvous) {
            return response()->json([
                'message' => 'Rendez-vous non trouvé.'
            ], 404);
        }

        return response()->json([
            'message' => 'Rendez-vous récupéré avec succès',
            'data' => $rendezvous,
        ], 200);
    }*/
   public function show($id)
{
    $rendezvous = Rendezvous::with([
        'client.user',
        'vehicule',
        'commande',
        'ligne_rendezvous.service',
        'ligne_rendezvous.technicien.user',
    ])->find($id);

    if (!$rendezvous) {
        return response()->json([
            'message' => 'Rendez-vous non trouvé.'
        ], 404);
    }

    $customData = [
        'id'                => $rendezvous->id,
        'num_rdv'           => 'RDV-' . str_pad($rendezvous->id, 3, '0', STR_PAD_LEFT),
         
        'statut_rdv'        => ucfirst($rendezvous->statut),
        'client'            => ($rendezvous->client && $rendezvous->client->user) 
                                ? trim(ucwords($rendezvous->client->user->prenom . ' ' . $rendezvous->client->user->nom)) 
                                : null,
        'date_heure'        => $rendezvous->date . ' · ' . substr($rendezvous->heure, 0, 5),
        'categorie_vehicule'=> $rendezvous->vehicule ? $rendezvous->vehicule->vehicule : null,
        'tarif_total'       => $rendezvous->tarifTotal ? $rendezvous->tarifTotal . ' MAD' : 'pas encore calculé',
        'num_cmd' =>   $rendezvous->commande 
                            ? 'CMD-' . str_pad($rendezvous->commande->id, 3, '0', STR_PAD_LEFT) 
                            : 'aucune commande associeé', // Affiche "CMD-001" ou "—" si aucune commande associée
        'description'       => $rendezvous->description,
        
        'lignes_rendezvous' => $rendezvous->ligne_rendezvous->map(function ($ligne, $index) {
            return [
                'id'         => $ligne->id, 
                'numero'     => $index + 1, 
                'service_id' => $ligne->service ? $ligne->service->id : null, 
                'service'    => $ligne->service ? ucfirst($ligne->service->nom) : null,
                
                // VÉRIFICATION SÉCURISÉE ICI : Évite le crash si pas de technicien
                'num_technicien' => $ligne->technicien 
                                ? 'TECH-' . str_pad($ligne->technicien->id, 3, '0', STR_PAD_LEFT) 
                                : null,
                                
                'technicien' => ($ligne->technicien && $ligne->technicien->user) 
                                ? trim(ucwords($ligne->technicien->user->prenom . ' ' . $ligne->technicien->user->nom)) 
                                : 'pas encore affecté',
                'duree'      => $ligne->duree ? $ligne->duree . ' min' : 'pas encore saisi',
                'tarif'      => $ligne->tarif ? $ligne->tarif . ' MAD' : 'pas encore saisi',
                'statut'     => ucfirst($ligne->statut),
            ];
        }),
    ];

    return response()->json([
        'message' => 'Rendez-vous récupéré avec succès',
        'data'    => $customData,
    ], 200);
}
    //pour creer un rendezvous
    public function store(Request $request)
    {
        //tu doit ajouter la validation pour service id.
        $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'description' => 'nullable|string',
            'commande_id' => 'nullable|exists:commandes,id',
           'lignes' => 'required|array|min:1|max:3',
              'lignes.*.service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'heure' => 'required|date_format:H:i',
        ]);
        $user = $request->user();
        $client = $user->client;

        $rendezvous = Rendezvous::create([
            'client_id' => $client->id,
            'vehicule_id' => $request->vehicule_id,
            'description' => $request->description,
            'commande_id' => $request->commande_id,
            'date' => $request->date,
            'heure' => $request->heure,

        ]);
        $rendezvous->ajouterLigneRendezvous($request->lignes);


        return response()->json(['message' => 'Rendezvous cree avec succes',
                                    'id' => $rendezvous->id
        ], 201);




}

public function NembrerendezvousparTechniciendujour(Request $request)
{


    $user = $request->user();
    if (!$user || !$user->technicien) {
        return response()->json([
            'message' => 'Technicien non authentifié.',
        ], 401);
    }

    $technicien = $user->technicien;
    $date=now()->format('Y-m-d');
    $nombreRendezvous = Rendezvous::whereHas('ligne_rendezvous', function ($query) use ($technicien) {
        $query->where('technicien_id', $technicien->id);
    })->whereDate('date', $date)->count();


    $lignesvalidees = LigneRendezvous::where('technicien_id', $technicien->id)
    ->where('statut', 'validé')
    ->whereHas('rendezvous', function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->count();
    
  $lignesencours = LigneRendezvous::where('technicien_id', $technicien->id)
    ->where('statut', 'en cours')
    ->whereHas('rendezvous', function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->count();
   $lignesterminees = LigneRendezvous::where('technicien_id', $technicien->id)
    ->where('statut', 'terminé')
    ->whereHas('rendezvous', function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->count();


    return response()->json([
        'message' => 'Nombre de rendez-vous du technicien pour la date spécifiée',
        'data' => [
            'nombre_rendezvous' => $nombreRendezvous,
            'lignes_validees' => $lignesvalidees,
            'lignes_en_cours' => $lignesencours,
            'lignes_terminées' => $lignesterminees,
        ],
    ], 200);
}
  


public function stats()
{
    $totalRendezvous = Rendezvous::count();
    $rendezvousEnAttente = Rendezvous::where('statut', 'en attente')->count();

    return response()->json([
        'message' => 'Statistiques des rendez-vous',
        'data' => [
            'total' => $totalRendezvous,
            'en_attente' => $rendezvousEnAttente,
        ],
    ], 200);
}

// Méthode pour récupérer les rendez-vous récents avec les infos du client
public function rendezVousRecents()
{
  
    $rendezVous = RendezVous::with('client')
        ->latest()
        ->take(40)
        ->get();

    // Transformation pour l'affichage épuré de ton composant
    $customData = $rendezVous->map(function ($rdv) {
        return [
            'id'          => $rdv->id,
                'num_rdv'     => 'RDV-' . str_pad($rdv->id, 3, '0', STR_PAD_LEFT), // Ex: RDV-001
            'nom_client'  => $rdv->client 
                ? trim(ucwords($rdv->client->user->prenom . ' ' . $rdv->client->user->nom)) 
                : 'Client inconnu', // Ex: Karim Benali
            
            // Formatage combiné : Date · Heure (Ex: 05/06/2025 · 09h00)
            'date_heure'  =>( $rdv->date && $rdv->heure) 
                ? Carbon::parse($rdv->date)->format('d/m/Y') . ' · ' . substr($rdv->heure, 0, 5) 
                : 'Date/Heure inconnue',
               

            'statut'      => $rdv->statut, // Ex: Terminé, Validé, Annulé
        ];
    });

    return response()->json([
        'message' => 'Rendez-vous récents récupérés avec succès',
        'data'    => $customData,
    ], 200);
}




}