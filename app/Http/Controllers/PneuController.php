<?php

namespace App\Http\Controllers;

use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use App\Models\Pneu;
use App\Models\Vehicule;
use App\Http\Requests\GetbyTrierPneuRequest;
class PneuController extends Controller
{
    //creer neveau pneu
public function store(Request $request)
{
    $validated = $request->validate([
        'marque'         => 'required|string|max:255',
        'modele'         => 'required|string|max:255',
        'largeur'        => 'required|integer',
        'hauteur'        => 'required|integer',
        'diametre_pouces'=> 'required|integer',
        'saison'         => 'required|string|max:255',
        'indice_charge'  => 'required|integer',
        'indice_vitesse' => 'required|string|max:10',
        'prix'           => 'required|numeric|min:0',
        'description'    => 'required|string',
        'quantite'       => 'required|integer|min:0',
        'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'vehicule_id'    => 'required|exists:vehicules,id',
    ]);

    // Gestion de l'image
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('pneus', 'public');
    }

    $pneu = Pneu::create($validated);

    return response()->json([
        'message' => 'Pneu créé avec succès',
        'data'    => $pneu,
    ], 201);
}

// obtenir par vehicule
public function getByVehicule($vehicule_id)
{
    $vehicule = Vehicule::findOrFail($vehicule_id);

    $pneus =$vehicule->pneus;

    return response()->json([
        'message' => 'Pneus récupérés avec succès',
        'data'    => $pneus,
    ], 200);
}
// trier par des parameters
public function getBytrier(GetbyTrierPneuRequest $request)
{
     
    
    $query = Pneu::query();

    // Filtrer par véhicule
    if ($request->filled('vehicule_id')) {
        $query->where('vehicule_id', $request->vehicule_id);
    }

    // Filtrer par saison
    if ($request->filled('saison')) {
        $query->where('saison', $request->saison);
    }

    // Filtrer par marque
    if ($request->filled('marque')) {
        $query->where('marque', 'LIKE', '%' . $request->marque . '%');
    }

    // Filtrer par dimension (largeur/hauteur/diametre)
    if ($request->filled('largeur')) {
        $query->where('largeur', $request->largeur);
    }

    if ($request->filled('hauteur')) {
        $query->where('hauteur', $request->hauteur);
    }

    if ($request->filled('diametre_pouces')) {
        $query->where('diametre_pouces', $request->diametre_pouces);
    }

    $pneus = $query->get();

    return response()->json([
        'message' => 'Pneus récupérés avec succès',
        'data'    => $pneus,
    ], 200);
}

}
