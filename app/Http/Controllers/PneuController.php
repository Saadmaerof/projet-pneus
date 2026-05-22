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


//obtenir les pneus d'un vehicule
public function getByVehicule($id)
{
   
    $vehicule = Vehicule::findOrFail($id);

    $pneus =$vehicule->pneus;

    return response()->json([
        'message' => 'Pneus récupérés avec succès',
        'data'    => $pneus,
    ], 200);
}


//trier les pneus par vehicule, saison, marque, dimension (largeur/hauteur/diametre)
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
