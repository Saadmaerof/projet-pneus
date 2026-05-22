<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\PneuController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\RendezvousController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//pour les clients
Route::get('/client', function (Request $request) {
    return $request->user()->load('client');
})->middleware('auth:sanctum'); 
Route::post('/client/register',[ClientController::class,'register']);
Route::post('/client/login',[ClientController::class,'login']);
Route::delete('/client/logout',[ClientController::class,'logout'])->middleware('auth:sanctum');     
Route::get('/client/index',[ClientController::class,'index']);




//pour les vehicules or categories
Route::get('/categories',[VehiculeController::class,'afficher']);
Route::post('/admin',[VehiculeController::class,'store']);
Route::delete('/admin/{id}',[VehiculeController::class,'delete']);
Route::post('/admin/{id}',[VehiculeController::class,'update']);
Route::get('/categories/{id}',[VehiculeController::class,'affiche']);


//pour les pneus
Route::get('/{vehicule_id}/pneus',[PneuController::class,'getByVehicule']);
Route::get('/pneus/pneustrierby',[PneuController::class,'getBytrier']);



//pour les commandes
Route::post('/commande/store',[CommandeController::class,'store'])->middleware('auth:sanctum');
Route::get('/commande/index',[CommandeController::class,'index']);
Route::put('/commande/updatestatut',[CommandeController::class,'updatestatut']);




//pour les rendezvous
Route::post('/rendezvous/store',[RendezvousController::class,'store'])->middleware('auth:sanctum');