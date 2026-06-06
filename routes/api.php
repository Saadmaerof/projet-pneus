<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\PneuController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\RendezvousController;
use App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//pour les users
Route::delete('/user/logout', [UserController::class,'logout'])->middleware('auth:sanctum');
Route::post('/user/login',[UserController::class,'login']); 

//pour Admin
Route::post('/admin/registerAdmin',[AdminController::class,'registerAdmin']);
Route::post('/admin/registerTechnicien',[AdminController::class,'registerTechnicien']);

//pour les clients
Route::get('/client', function (Request $request) {
    return $request->user()->load('client');
})->middleware('auth:sanctum'); 
Route::post('/client/register',[ClientController::class,'register']);
Route::post('/client/login',[ClientController::class,'login']);    
Route::get('/client/index',[ClientController::class,'index']);




//pour les vehicules or categories
Route::get('/categories',[VehiculeController::class,'index']);
Route::post('/admin',[VehiculeController::class,'store']);
Route::delete('/admin/{id}',[VehiculeController::class,'delete']);
Route::post('/admin/{id}',[VehiculeController::class,'update']);
Route::get('/categories/{id}',[VehiculeController::class,'show']);


//pour les pneus
Route::get('/pneus',[PneuController::class,'index']);
Route::get('/{vehicule_id}/pneus',[PneuController::class,'getByVehicule']);

Route::post('/pneus/store',[PneuController::class,'store']);
Route::get('/pneus/pneustrierby',[PneuController::class,'getBytrier']);


//pour les commandes
Route::post('/commande/store',[CommandeController::class,'store'])->middleware('auth:sanctum');
Route::get('/commande/index',[CommandeController::class,'index']);
Route::put('/commande/updatestatut',[CommandeController::class,'updatestatut']);




//pour les rendezvous
Route::post('/rendezvous/store',[RendezvousController::class,'store'])->middleware('auth:sanctum');