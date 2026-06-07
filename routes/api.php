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
Route::post('/admin/registerAdmin',[AdminController::class,'registerAdmin']);
Route::post('/admin/registerTechnicien',[AdminController::class,'registerTechnicien']);

// pour les techniciens
Route::get('/techniciens',[TechnicienController::class,'index']);
Route::put('/techniciens/{id}/changer-statut', [TechnicienController::class, 'changerStatut']);
Route::put('/techniciens/{id}/changer-disponibilite', [TechnicienController::class, 'changerDisponibilite']);
Route::get('/technicien/rendezvous',[TechnicienController::class, 'rendezvousParTechnicien'])->middleware('auth:sanctum');

//pour les clients
Route::get('/client', function (Request $request) {
    return $request->user()->load('client');
})->middleware('auth:sanctum'); 
Route::post('/client/register',[ClientController::class,'register']);
Route::post('/client/login',[ClientController::class,'login']);    
Route::get('/client/index',[ClientController::class,'index']);
Route::put('/client/{id}/changer-statut',[ClientController::class,'changerStatut']);
Route::get('/client/stats',[ClientController::class,'stats']);




//pour les vehicules or categories
Route::get('/categories',[VehiculeController::class,'index']);
Route::post('/categories',[VehiculeController::class,'store']);
Route::delete('/categories/{id}',[VehiculeController::class,'delete']);
Route::post('/categories/{id}',[VehiculeController::class,'update']);
Route::get('/categories/{id}',[VehiculeController::class,'show']);


//pour les pneus
Route::get('/pneus',[PneuController::class,'index']);
Route::get('/pneus/{id}',[PneuController::class,'show']);
Route::post('/pneus/store',[PneuController::class,'store']);
Route::post('/pneus/{id}',[PneuController::class,'update']);
Route::delete('/pneus/{id}',[PneuController::class,'destroy']);

Route::get('/{vehicule_id}/pneus',[PneuController::class,'getByVehicule']);
Route::get('/pneustrierby',[PneuController::class,'getBytrier']);




//pour les commandes
Route::post('/commande/store',[CommandeController::class,'store'])->middleware('auth:sanctum');
Route::get('/commande/index',[CommandeController::class,'index']);
Route::get('/commande/{id}',[CommandeController::class,'show']);
Route::put('/commande/updatestatut',[CommandeController::class,'updatestatut']);







//pour les rendezvous
Route::get('/rendezvous',[RendezvousController::class,'index']);
Route::get('/rendezvous/{id}',[RendezvousController::class,'show']);
Route::post('/rendezvous/store',[RendezvousController::class,'store'])->middleware('auth:sanctum');

// pour les lignes de rendez-vous
Route::post('/ligne-rendezvous/{id}/affecter-technicien', [LigneRendezvousController::class, 'affecterTechnicien']);
Route::put('/ligne-rendezvous/{id}/changer-statut', [LigneRendezvousController::class, 'changerStatutLigne'])->middleware('auth:sanctum');
