<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pneu;
use App\Models\Vehicule;
use App\Http\Requests\GetbyTrierPneuRequest;
class PneuController extends Controller
{



// function index qui peux afficher tout les pneus ou par vehicule_id.

    public function index(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'nullable|exists:vehicules,id',
        ]);

        $query = Pneu::query();

        if (!empty($validated['vehicule_id'])) {
            $query->where('vehicule_id', $validated['vehicule_id']);
        }

        $pneus = $query->get();

        return response()->json([
            'message' => 'Pneus récupérés avec succès',
            'data' => $pneus,
        ], 200);
    }





    //creer neveau pneu
public function store(Request $request)
{
    $validated = $request->validate([
        'marque'           => 'required|string|max:255',
    'modele'           => 'required|string|max:255',
    'largeur'          => 'required|integer|min:10',
    'hauteur'          => 'required|integer|min:10',
    'diametre_pouces'  => 'required|integer|min:1',
    'saison'           => 'required|string|in:Été,Hiver,4 Saisons,All Season', // Adaptez les valeurs selon vos besoins
    'indice_charge'    => 'required|integer|min:0',
    'indice_vitesse'   => 'required|string|max:5', // Souvent une ou deux lettres (ex: V, W, Y)
    'prix'             => 'required|numeric|min:0',
    'description'      => 'required|string',
    'quantite'         => 'nullable|integer|min:0', // Optionnel car il y a un default(0) en BDD
    'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validation de fichier image (max 2Mo)
    'vehicule_id'      => 'required|exists:vehicules,id', // Vérifie que le véhicule existe bien dans la table 'vehicules'
       
    ]);

    // Gestion de l'image
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('pneus', 'public');
        $validated['image'] = 'storage/app/public/' . $imagePath;
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
