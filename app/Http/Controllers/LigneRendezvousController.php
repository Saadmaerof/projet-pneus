<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LigneRendezvous;
use App\Models\Rendezvous;
use App\Models\Service;
use App\Models\Technicien;

class LigneRendezvousController extends Controller
{
    // Affecter un technicien et définir le statut (validé ou annulé)
    public function affecterTechnicien(Request $request, $id)
    {
        $ligne = LigneRendezvous::find($id);
        if (!$ligne) {
            return response()->json(['message' => 'Ligne de rendez-vous non trouvée.'], 404);
        }

        $validated = $request->validate([
            'technicien_id' => 'nullable|exists:techniciens,id',
            'statut' => 'required|string|in:validé,annulé,valide,annule',
        ]);

        $statut = $validated['statut'];
        if ($statut === 'valide') {
            $statut = 'validé';
        } elseif ($statut === 'annule') {
            $statut = 'annulé';
        }

        if ($statut === 'validé' && empty($validated['technicien_id'])) {
            return response()->json([
                'message' => 'Un technicien est requis pour valider la ligne.',
            ], 422);
        }

        if ($statut === 'annulé') {
            $ligne->technicien_id = null;
        } else {
            $ligne->technicien_id = $validated['technicien_id'];
        }

        $ligne->statut = $statut;
        $ligne->save();

        $ligne->rendezvous->actualiserStatut();

        return response()->json([
            'message' => 'Ligne de rendez-vous mise à jour.',
        ], 200);
    }

    public function changerStatutLigne(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->technicien) {
            return response()->json(['message' => 'Technicien non authentifié.'], 401);
        }

        $ligne = LigneRendezvous::find($id);
        if (!$ligne) {
            return response()->json(['message' => 'Ligne de rendez-vous non trouvée.'], 404);
        }

        if ($ligne->technicien_id !== $user->technicien->id) {
            return response()->json(['message' => 'Accès refusé à cette ligne.'], 403);
        }

        $status = $ligne->statut;
        if ($status === 'validé') {
            $ligne->statut = 'en cours';
        } elseif ($status === 'en cours') {
            $ligne->statut = 'terminé';
        } else {
            return response()->json([
                'message' => 'Le statut de la ligne doit être validé ou en cours pour être changé.',
            ], 422);
        }

        $ligne->save();
        $ligne->rendezvous->actualiserStatut();

        return response()->json([
            'message' => 'Statut de la ligne de rendez-vous mis à jour.',
            'data' => $ligne,
        ], 200);
    }
}
