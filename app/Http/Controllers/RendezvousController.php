<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Rendezvous;

class RendezvousController extends Controller
{
    //index : filtrer les rendezvous par date, statut et technicien (si statut = validé)
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'statut' => 'nullable|string|in:en attente,validé,en cours,terminé,annulé',
            'technicien_nom' => 'nullable|string|max:255',
        ]);

        $query = Rendezvous::with(['client.user', 'vehicule', 'techniciens.user', 'commande']);

        if (!empty($validated['date'])) {
            $query->whereDate('date', $validated['date']);
        }

        if (!empty($validated['statut'])) {
            $query->where('statut', $validated['statut']);
        }

        if (!empty($validated['technicien_nom']) && !empty($validated['statut']) && in_array($validated['statut'], ['validé', 'en cours', 'terminé'])) {
            $search = '%'.$validated['technicien_nom'].'%';

            $query->whereHas('techniciens.user', function ($q) use ($search) {
                $q->where('nom', 'like', $search)
                  ->orWhere('prenom', 'like', $search);
            });
        }

        $rendezvous = $query->get();

        return response()->json([
            'message' => $rendezvous->isEmpty()
                ? 'Aucun rendez-vous trouvé.'
                : 'Rendez-vous récupérés avec succès',
            'data' => $rendezvous,
        ], 200);
    }

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

        return response()->json([
            'message' => 'Rendez-vous récupéré avec succès',
            'data' => $rendezvous,
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


        return response()->json(['message' => 'Rendezvous cree avec succes'], 201);




}
}