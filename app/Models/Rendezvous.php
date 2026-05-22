<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rendezvous extends Model
{
    protected $table = 'rendez_vous';
protected $guarded = ['id','updated_at','	created_at'];

public function client()
{
    return $this->belongsTo(Client::class); 


}
public function commande()
{
    return $this->hasOne(Commande::class);

}

public function techniciens()
{
    return $this->belongsToMany(Technicien::class, 'ligne_rendezvous', 'rendezvous_id', 'technicien_id')
                ->withPivot('service_id')
                ->withTimestamps();



}
public function services()
{
    return $this->belongsToMany(Service::class, 'ligne_rendezvous', 'rendezvous_id', 'service_id')
                ->withPivot('technicien_id')
                ->withTimestamps();

}
public function vehicule()
{
    return $this->belongsTo(Vehicule::class);

}
public function ligne_rendezvous()
{
    return $this->hasMany(LigneRendezvous::class);




}

}