<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pneu extends Model
{

protected $guarded = ['id','updated_at','	created_at'];


public function vehicule(){

return $this->belongsTo(Vehicule::class);

}
public function commandes(){
return $this->belongsToMany(Commande::class, 'ligne_commandes', 'pneu_id', 'commande_id')
                ->withPivot('quantite', 'prix_unitaire')
                ->withTimestamps();
}

public function ligne_commandes(){
return $this->hasMany(Ligne_commande::class);


}

//les méthodes métier





}