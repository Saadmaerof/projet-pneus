<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculeRequest;
use App\Http\Requests\UpdateVehiculeRequest;
use App\Models\Vehicule;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Laravel\Prompts\Title;
use Illuminate\Support\Facades\Storage;
class VehiculeController extends Controller
{
    
//creer neveau vehicule
     public function store(StoreVehiculeRequest $request)
{

$imagePath = $request->file('image')->store('categorie_vehicules', 'public');
    Vehicule::create([
        'vehicule'    => $request->vehicule,
        'description' => $request->description,
        'image'       =>$imagePath
    ]);

    return response()->json(['success'=>'true', 'message'=>'Catégorie créée avec succès.']);
}

//afficher tous les vehicules
public function afficher(){

$categories = Vehicule::all();

return response()->json($categories);

}

//afficher un seul vehicule
public function affiche($id){

$categorie = Vehicule::find($id);

 if (!$categorie) {
        return response()->json([
            'success' => false,
            'message' => 'Catégorie non trouvée.'
        ], 404);
    }



return response()->json($categorie);

}

// supprimer un vehicule
public function delete($id)
{
    $vehicule = Vehicule::find($id);

    if (!$vehicule) {
        return response()->json([
            'success' => false,
            'message' => 'Catégorie non trouvée.'
        ], 404);
    }
     // Supprimer  image si elle existe
        if ($vehicule->image && Storage::disk('public')->exists($vehicule->image)) {
            Storage::disk('public')->delete($vehicule->image);
        }

    $vehicule->delete();


    return response()->json([
        'success' => true,
        'message' => 'Catégorie supprimée avec succès.'
    ]);
}

//modifier un vehicule
public function update(UpdateVehiculeRequest $request, $id)
{
   
    $categorie = Vehicule::find($id);
    if (!$categorie) {
        return response()->json([
            'success' => false,
            'message' => 'Catégorie non trouvée.'
        ], 404);
    }

    $data = [];
    
    if ($request->has('vehicule')) {
        $data['vehicule'] = $request->vehicule;
    }
    
    if ($request->has('description')) {
        $data['description'] = $request->description;
    }
    
    if ($request->hasFile('image')) {
        // Supprimer l'ancienne image si elle existe
        if ($categorie->image && Storage::disk('public')->exists($categorie->image)) {
            Storage::disk('public')->delete($categorie->image);
        }
        $imagePath = $request->file('image')->store('categorie_vehicules', 'public');
        $data['image'] = $imagePath;
    }

    $categorie->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Catégorie modifiée avec succès.'
    ]);
}




}
