<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\PneuController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\LigneRendezvousController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//pour les users
Route::delete('/user/logout', [UserController::class,'logout'])->middleware('auth:sanctum');
Route::post('/user/login',[UserController::class,'login']); 

//pour Admin
Route::post('/admin/ajouterTechnicien',[AdminController::class,'ajouterTechnicien'])->middleware(['auth:sanctum', 'isAdmin']);;

// pour les techniciens
Route::get('/techniciens',[TechnicienController::class,'index'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::put('/techniciens/{id}/changer-statut', [TechnicienController::class, 'changerStatut'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::put('/techniciens/{id}/changer-disponibilite', [TechnicienController::class, 'changerDisponibilite'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::get('/technicien/rendezvous',[TechnicienController::class, 'rendezvousParTechnicien'])->middleware('auth:sanctum');
Route::get('/stats/technicien',[TechnicienController::class, 'stats'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('/techniciens/{service_Id}', [TechnicienController::class, 'techniciensbyservice'])->middleware(['auth:sanctum', 'isAdmin']);;


//pour les clients
Route::get('/client', function (Request $request) {
    return $request->user()->load('client');
})->middleware('auth:sanctum'); 
Route::post('/client/register',[ClientController::class,'register']);
Route::post('/client/login',[ClientController::class,'login']); 

Route::get('/client/index',[ClientController::class,'index'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::put('/client/{id}/changer-statut',[ClientController::class,'changerStatut'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::get('/stats/client',[ClientController::class,'stats'])->middleware(['auth:sanctum', 'isAdmin']);;




//pour les vehicules or categories
Route::get('/categories',[VehiculeController::class,'index']);
Route::post('/categories',[VehiculeController::class,'store'])->middleware(['auth:sanctum', 'isAdmin']);
Route::delete('/categories/{id}',[VehiculeController::class,'delete'])->middleware(['auth:sanctum', 'isAdmin']);
Route::post('/categories/{id}',[VehiculeController::class,'update'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('/categories/{id}',[VehiculeController::class,'show'])->middleware(['auth:sanctum', 'isAdmin']);


//pour les pneus
Route::get('/admin/pneus',[PneuController::class,'index']);
Route::get('/pneus/{id}',[PneuController::class,'show']);
Route::post('/store/pneus',[PneuController::class,'store'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::post('/pneus/{id}',[PneuController::class,'update'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::delete('/pneus/{id}',[PneuController::class,'destroy'])->middleware(['auth:sanctum', 'isAdmin']);;

Route::get('/{vehicule_id}/pneus',[PneuController::class,'getByVehicule']);
Route::get('/pneustrierby',[PneuController::class,'getBytrier']);




//pour les commandes
Route::post('/commande/store',[CommandeController::class,'store'])->middleware('auth:sanctum');
Route::get('/commande/index',[CommandeController::class,'index'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::get('/commande/{id}',[CommandeController::class,'show'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::put('/commande/updatestatut',[CommandeController::class,'updatestatut'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::get('/stats/commande',[CommandeController::class,'stats'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('/recents/commande',[CommandeController::class,'dernieresCommandes'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('/historiquecommandes',[CommandeController::class,'historiquecommandes'])->middleware('auth:sanctum');






//pour les rendezvous
Route::get('/rendezvous',[RendezvousController::class,'index'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::get('/rendezvous/{id}',[RendezvousController::class,'show'])->middleware(['auth:sanctum', 'isAdmin']);;
Route::post('/rendezvous/store',[RendezvousController::class,'store'])->middleware('auth:sanctum');
Route::get('/rendezvous/technicien/count',[RendezvousController::class,'NembrerendezvousparTechniciendujour'])->middleware('auth:sanctum');
Route::get('/stats/rendezvous',[RendezvousController::class,'stats'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('/recents/rendezvous',[RendezvousController::class,'rendezVousRecents'])->middleware(['auth:sanctum', 'isAdmin']);

// pour les lignes de rendez-vous
Route::post('/ligne-rendezvous/{id}/affecter-technicien', [LigneRendezvousController::class, 'affecterTechnicien'])->middleware(['auth:sanctum', 'isAdmin']);
Route::put('/ligne-rendezvous/{id}/changer-statut', [LigneRendezvousController::class, 'changerStatutLigne'])->middleware('auth:sanctum');
