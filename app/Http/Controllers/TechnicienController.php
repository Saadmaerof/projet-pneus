<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Technicien;
use App\Models\Rendezvous;
use App\Models\Service;
class TechnicienController extends Controller
{



    /*public function index(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'nullable|string|max:255',
            'statut' => 'nullable|string|in:actif,bloque,bloqué',
            'disponibilite' => 'nullable|boolean',
        ]);

        $query = Technicien::with('user');

        if (!empty($validated['nom'])) {
            $search = '%' . trim($validated['nom']) . '%';
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nom', 'like', $search)
                  ->orWhere('prenom', 'like', $search);
            });
        }

        if (array_key_exists('disponibilite', $validated) && $validated['disponibilite'] !== null) {
            $query->where('disponibilite', $validated['disponibilite']);
        }

        if (!empty($validated['statut'])) {
            $statut = $validated['statut'] === 'bloqué' ? 'bloque' : $validated['statut'];

            if (Schema::hasColumn('techniciens', 'statut')) {
                $query->where('statut', $statut);
            } elseif ($statut === 'bloque') {
                $query->whereRaw('false');
            }
        }

        $techniciens = $query->get();

        return response()->json([
            'message' => 'Techniciens récupérés avec succès',
            'data' => $techniciens,
        ], 200);
    }*/

        public function index(Request $request)
{
    $validated = $request->validate([
        'nom'           => 'nullable|string|max:255',
        'statut'         => 'nullable|string|in:actif,bloque,bloqué',
        'disponibilite' => 'nullable|boolean',
    ]);

    $query = Technicien::with('user');

    if (!empty($validated['nom'])) {
        $search = '%' . trim($validated['nom']) . '%';
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('nom', 'like', $search)
              ->orWhere('prenom', 'like', $search);
        });
    }

    if (array_key_exists('disponibilite', $validated) && $validated['disponibilite'] !== null) {
        $query->where('disponibilite', $validated['disponibilite']);
    }

    if (!empty($validated['statut'])) {
        $statut = $validated['statut'] === 'bloqué' ? 'bloque' : $validated['statut'];

        if (Schema::hasColumn('techniciens', 'statut')) {
            $query->where('statut', $statut);
        } elseif ($statut === 'bloque') {
            $query->whereRaw('false');
        }
    }

    $techniciens = $query->get();

    // Transformation pour renvoyer uniquement les champs visibles sur la maquette
    $customData = $techniciens->map(function ($technicien) {
        return [
            'id'            => $technicien->id,
            'id_user'       => $technicien->users_id,
            'num_technicien' => 'TECH-' . str_pad($technicien->id, 3, '0', STR_PAD_LEFT), // Ex: TECH-001
            'nom_complet'   => ($technicien->user) 
                                ? trim(ucwords($technicien->user->prenom . ' ' . $technicien->user->nom)) 
                                : null, // Ex: Ali Tahiri
            'email'         => $technicien->user ? $technicien->user->email : null, // Ex: a.tahiri@mail.ma
            'statut_compte' => $technicien->statut, // Ex: bloqué, actif
            'disponibilite' => $technicien->disponibilite == 1 ? 'Disponible' : 'Indisponible', // Ex: Indisponible
        ];
    });

    return response()->json([
        'message' => 'Techniciens récupérés avec succès',
        'data'    => $customData,
    ], 200);
}

    public function changerStatut(Request $request, $id)
    {
        $validated = $request->validate([
            'statut' => 'required|string|in:actif,bloque,bloqué',
        ]);

        $technicien = Technicien::find($id);
        if (!$technicien) {
            return response()->json(['message' => 'Technicien non trouvé.'], 404);
        }

        $statut = $validated['statut'];

        if (!Schema::hasColumn('techniciens', 'statut')) {
            return response()->json([
                'message' => 'Le champ statut n\'existe pas sur les techniciens.',
            ], 500);
        }

        $technicien->statut = $statut;
        
        if ($statut === 'bloque') {
            $technicien->disponibilite = false;
        }
        
        $technicien->save();

        return response()->json([
            'message' => 'Statut du technicien mis à jour avec succès',
            'data' => $technicien,
        ], 200);
    }

    public function changerDisponibilite(Request $request, $Id)
{
    $validated = $request->validate([
        'disponibilite' => 'required|boolean',
    ]);

    $technicien = Technicien::find($Id);
    if (!$technicien) {
        return response()->json(['message' => 'Technicien non trouvé.'], 404);
    }

    $technicien->disponibilite = $validated['disponibilite'];
    $technicien->save();

    return response()->json([
        'message' => 'Disponibilité du technicien mise à jour',
        'data' => $technicien,
    ], 200);
}

   public function rendezvousParTechnicien(Request $request)
{
    $user = $request->user();
    if (!$user || !$user->technicien) {
        return response()->json(['message' => 'Technicien non authentifié.'], 401);
    }

    $technicien = $user->technicien;
  $date=now()->format('Y-m-d');
   $rendezvous = Rendezvous::with([
    'client.user:id,nom,prenom',
    'vehicule:id,vehicule',
    'ligne_rendezvous.service:id,nom',
    'ligne_rendezvous.technicien.user:id,nom,prenom',
])->whereHas('ligne_rendezvous', function ($query) use ($technicien) {
    $query->where('technicien_id', $technicien->id);
})->whereDate('date', $date)->get();

$data = $rendezvous->map(function ($rdv) {
    return [
        'id'          => $rdv->id,
        'heure'       => $rdv->heure,
        'statut'      => $rdv->statut,
        'description' => $rdv->description,
        'client' => [
            'nom'    => $rdv->client?->user?->nom    ?? '—',
            'prenom' => $rdv->client?->user?->prenom ?? '—',
        ],
        'vehicule'         => $rdv->vehicule?->vehicule ?? '—',
        'ligne_rendezvous' => $rdv->ligne_rendezvous->map(function ($ligne) {
            return [
                'id'     => $ligne->id,
                'statut' => $ligne->statut,
                'duree'  => $ligne->duree,
                'tarif'  => $ligne->tarif,
                'service' => $ligne->service?->nom ?? '—',  // ✅ null safe
                'technicien' => [
                    'users_id' => $ligne->technicien?->users_id          ?? null,
                    'nom'      => $ligne->technicien?->user?->nom         ?? '—',
                    'prenom'   => $ligne->technicien?->user?->prenom      ?? '—',
                ],
            ];
        }),
    ];
});

    return response()->json([
        'message' => 'Rendez-vous du technicien récupérés avec succès',
        'users_id concerne' => $user->id,
        'data'    => $data,
    ], 200);
}



    public function stats()
{
    $totalTechniciens = Technicien::count();
    $techniciensdisponible = Technicien::where('disponibilite', true)->count();

    return response()->json([
        'message' => 'Statistiques des techniciens',
        'data' => [
            'total' => $totalTechniciens,
            'disponible' => $techniciensdisponible,
           
        ],
    ], 200);
}


// pour récupérer les techniciens par service
public function techniciensbyservice($service_Id)
{
   $service= Service::find($service_Id);
   if (!$service) {
        return response()->json(['message' => 'Service non trouvé.'], 404);
    }
    $techniciens = $service->techniciens;
    $techniciens = $techniciens->map(function ($technicien) {
        return [
            'technicien_id'       => $technicien->id,
            'num_technicien' => 'TECH-' . str_pad($technicien->id, 3, '0', STR_PAD_LEFT), // Ex: TECH-001
            'nom_complet'   => ($technicien->user) 
                                ? trim(ucwords($technicien->user->prenom . ' ' . $technicien->user->nom)) 
                                : null, // Ex: Ali Tahiri
        ];
    });


    return response()->json([
        'message' => 'Techniciens associés au service récupérés avec succès',
        'data' => $techniciens,
    ], 200);
}



}
