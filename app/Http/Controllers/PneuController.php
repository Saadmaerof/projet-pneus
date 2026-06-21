<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pneu;
use App\Models\Vehicule;
use App\Http\Requests\GetbyTrierPneuRequest;
use Illuminate\Support\Facades\Storage;
class PneuController extends Controller
{



// function index qui peux afficher tout les pneus ou par vehicule_id.
   /* public function index(Request $request)
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
    }*/
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

    $customData = $pneus->map(function ($pneu) {
        return [
            'id'             => $pneu->id,
            'marque'         => $pneu->marque,        
            'modele'         => $pneu->modele,        
            'dimensions'     => $pneu->largeur . '/' . $pneu->hauteur . ' R' . $pneu->diametre_pouces, 
            'largeur'        => $pneu->largeur,          // <-- AJOUTÉ : Pour éviter les split en front
            'hauteur'        => $pneu->hauteur,          // <-- AJOUTÉ : Pour éviter les split en front
            'diametre_pouces'=> $pneu->diametre_pouces,  // <-- AJOUTÉ : Pour éviter les split en front
            'saison'         => $pneu->saison,        
            'charge_vitesse' => $pneu->indice_charge . '/' . $pneu->indice_vitesse, 
            'indice_charge'  => $pneu->indice_charge,   // <-- AJOUTÉ : Pour le formulaire front
            'indice_vitesse' => $pneu->indice_vitesse,  // <-- AJOUTÉ : Pour le formulaire front
            'prix'           => $pneu->prix,          
            'quantite'       => $pneu->quantite,      
            'categorie'      => $pneu->vehicule->vehicule, 
            'categorie_id'   => $pneu->vehicule->id,     // <-- AJOUTÉ : Pour lier le select HTML
            'description'    => $pneu->description,      // <-- AJOUTÉ : Pour pré-remplir la description
            'image'          => $pneu->image ? asset('storage/' . $pneu->image) : null // <-- AJOUTÉ : L'image est maintenant là !
        ];
    });

    return response()->json([
        'message' => 'Pneus récupérés avec succès',
        'data' => $customData,
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
    'image'            => 'nullable|image|mimes:jpeg,jfif,png,jpg,webp,Avif|max:2048', // Validation de fichier image (max 2Mo)
    'vehicule_id'      => 'required|exists:vehicules,id', // Vérifie que le véhicule existe bien dans la table 'vehicules'
       
    ]);

    // Gestion de l'image
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('pneus', 'public');
        $validated['image'] = $imagePath;
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

////obtenir les pneus d'un vehicule plus vendu
public function getByVehiculeRecommande($id)
{
   
    $vehicule = Vehicule::findOrFail($id);

    $pneus =$vehicule->pneus ->take(4)->get();

    return response()->json([
        'message' => 'Pneus récupérés avec succès',
        'data'    => $pneus,
    ], 200);
}
// afficher un pneu unique
/*public function show($id)
{
    $pneu = Pneu::with('vehicule')->find($id);
   

    if (!$pneu) {
        return response()->json([
            'success' => false,
            'message' => 'Pneu non trouvé.'
        ], 404);
    }

    // Calculer le nombre total de pneus vendus (somme des quantités dans ligne_commandes)
    $nombreVentes = $pneu->ligne_commandes()->sum('quantite');

    return response()->json([
        'message' => 'Pneu récupéré avec succès',
        'data'    => [
            'pneu' => $pneu,
            'nombre_ventes' => (int) $nombreVentes,
        ],
    ], 200);
}*/

public function show($id)
{
    // On récupère le pneu avec sa relation véhicule
    $pneu = Pneu::with('vehicule')->find($id);

    if (!$pneu) {
        return response()->json([
            'success' => false,
            'message' => 'Pneu non trouvé.'
        ], 404);
    }

    $nombreVentes = $pneu->ligne_commandes()->sum('quantite') ?? 0;

    // Transformation pour correspondre à la fiche technique de la maquette
    $customData = [
        'id'               => $pneu->id, 
        'marque'           => $pneu->marque,                            // <-- AJOUTÉ : Pour ton formulaire de modification
        'modele'           => $pneu->modele,                            // <-- AJOUTÉ : Pour ton formulaire de modification
        'titre'            => trim($pneu->marque . ' · ' . $pneu->modele), // Ex: Goodyear · Vector 4Seasons
        'saison'           => $pneu->saison,                            // Ex: Toutes saisons
        'categorie'        => $pneu->vehicule->vehicule,                // Ex: SUV / 4x4
        'categorie_id'     => $pneu->vehicule->id, 
        'dimensions'       => $pneu->largeur . '/' . $pneu->hauteur . ' R' . $pneu->diametre_pouces, // Ex: 185/60 R15
        'largeur'          => $pneu->largeur,                           // <-- AJOUTÉ : Plus besoin de découper la chaîne en front
        'hauteur'          => $pneu->hauteur,                           // <-- AJOUTÉ 
        'diametre_pouces'  => $pneu->diametre_pouces,                  // <-- AJOUTÉ 
        'prix'             => $pneu->prix,                              // Ex: 680
        'indice_charge'    => $pneu->indice_charge,                     // Ex: 88
        'indice_vitesse'   => $pneu->indice_vitesse,                    // Ex: V
        'quantite_stock'   => $pneu->quantite,                          // Ex: 20
        'nombre_ventes'    => $nombreVentes,                            // Ex: 27
        'description'      => $pneu->description,                       // Ex: Homologué été et hiver.
        'image'            => $pneu->image ? asset('storage/' . $pneu->image) : null
    ];

    return response()->json([
        'message' => 'Pneu récupéré avec succès',
        'data' => $customData,
    ], 200);
}

//met a jour un pneu
public function update(Request $request, $id)
{
    $pneu = Pneu::find($id);

    if (!$pneu) {
        return response()->json([
            'success' => false,
            'message' => 'Pneu non trouvé.'
        ], 404);
    }

    $validated = $request->validate([
        'marque'           => 'sometimes|string|max:255',
        'modele'           => 'sometimes|string|max:255',
        'largeur'          => 'sometimes|integer|min:10',
        'hauteur'          => 'sometimes|integer|min:10',
        'diametre_pouces'  => 'sometimes|integer|min:1',
        'saison'           => 'sometimes|string|in:Été,Hiver,4 Saisons,All Season',
        'indice_charge'    => 'sometimes|integer|min:0',
        'indice_vitesse'   => 'sometimes|string|max:5',
        'prix'             => 'sometimes|numeric|min:0',
        'description'      => 'sometimes|string',
        'quantite'         => 'nullable|integer|min:0',
        'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp,Avif|max:2048',
        'vehicule_id'      => 'sometimes|exists:vehicules,id',
    ]);

    // Gestion de l'image si fournie
    if ($request->hasFile('image')) {
        // Supprimer l'ancienne image si elle existe
        $oldPath = $pneu->image;
        if ($oldPath) {
            if (str_starts_with($oldPath, 'storage/app/public/')) {
                $oldPath = substr($oldPath, strlen('storage/app/public/'));
            }
            Storage::disk('public')->delete($oldPath);
        }

        $imagePath = $request->file('image')->store('pneus', 'public');
        $validated['image'] = $imagePath;
    }

    $pneu->update($validated);

    return response()->json([
        'message' => 'Pneu mis à jour avec succès',
        'data'    => $pneu->fresh(),
    ], 200);
}

// supprimer un pneu
public function destroy($id)
{
    $pneu = Pneu::find($id);

    if (!$pneu) {
        return response()->json([
            'success' => false,
            'message' => 'Pneu non trouvé.'
        ], 404);
    }

    // Supprimer l'image si elle existe
    if ($pneu->image) {
        $imagePath = $pneu->image;
        if (str_starts_with($imagePath, 'storage/app/public/')) {
            $imagePath = substr($imagePath, strlen('storage/app/public/'));
        }
        Storage::disk('public')->delete($imagePath);
    }

    $pneu->delete();

    return response()->json([
        'success' => true,
        'message' => 'Pneu supprimé avec succès.'
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
