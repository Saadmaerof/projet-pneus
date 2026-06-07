<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Technicien;
use App\Models\Rendezvous;

class TechnicienController extends Controller
{



    public function index(Request $request)
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

        $statut = $validated['statut'] === 'bloqué' ? 'bloque' : $validated['statut'];

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

    public function changerDisponibilite(Request $request, $id)
    {
        $validated = $request->validate([
            'disponibilite' => 'required|boolean',
        ]);

        $technicien = Technicien::find($id);
        if (!$technicien) {
            return response()->json(['message' => 'Technicien non trouvé.'], 404);
        }

        $technicien->disponibilite = $validated['disponibilite'];
        $technicien->save();

        return response()->json([
            'message' => 'Disponibilité du technicien mise à jour avec succès',
            'data' => $technicien,
        ], 200);
    }

    public function rendezvousParTechnicien(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->technicien) {
            return response()->json([
                'message' => 'Technicien non authentifié.',
            ], 401);
        }

        $technicien = $user->technicien;

        $rendezvous = Rendezvous::with([
            'client.user',
            'vehicule',
            'ligne_rendezvous.service',
            'ligne_rendezvous.technicien.user',
            'techniciens.user',
        ])->whereHas('ligne_rendezvous', function ($query) use ($technicien) {
            $query->where('technicien_id', $technicien->id);
        })->get();

        return response()->json([
            'message' => 'Rendez-vous du technicien récupérés avec succès',
            'data' => $rendezvous,
        ], 200);
    }
}
