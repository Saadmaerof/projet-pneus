<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\PneuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); 
*/



//pour les vehicules or categories
Route::get('/',[VehiculeController::class,'afficher']);
Route::post('/admin',[VehiculeController::class,'store']);
Route::delete('/admin/{id}',[VehiculeController::class,'delete']);
Route::post('/admin/{id}',[VehiculeController::class,'update']);
Route::get('/{id}',[VehiculeController::class,'affiche']);


//pour les pneus
Route::get('/{vehicule_id}/pneus',[PneuController::class,'getByVehicule']);
Route::get('pneus/pneustrierby',[PneuController::class,'getBytrier']);