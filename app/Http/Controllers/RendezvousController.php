<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Rendezvous;

class RendezvousController extends Controller
{
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

        return response()->json(['message' => 'Rendezvous cree avec succes'], 201);




}
}