<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rendezvous extends Model
{


    protected $table = 'rendezvous';
protected $guarded = ['id','updated_at','	created_at'];

// Relation 
public function client()
{
    return $this->belongsTo(Client::class); 


}
public function commande()
{
    return $this->belongsTo(Commande::class, 'commande_id');
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



//les méthodes métier
public function actualiserStatut(): self
{
    $statuts = $this->ligne_rendezvous()->pluck('statut')->map(function ($value) {
        return mb_strtolower($value);
    })->filter()->unique()->values()->all();

    if (empty($statuts)) {
        return $this;
    }

    if (count($statuts) === 1 && $statuts[0] === 'terminé') {
        $this->statut = 'terminé';
    } elseif (in_array('en attente', $statuts, true)) {
        $this->statut = 'en attente';
    } elseif (in_array('en cours', $statuts, true)) {
        $this->statut = 'en cours';
    } elseif (count($statuts) === 1 && $statuts[0] === 'validé') {
        $this->statut = 'validé';
    } elseif (in_array('annulé', $statuts, true)) {
        $this->statut = 'annulé';
    }

    $this->save();
    return $this;
}


// ajouterLigneRendezvous( service_id:int)
 public function ajouterLigneRendezvous(array $lignes): void
    {
          foreach ($lignes as $ligne) {
            $service = Service::find($ligne['service_id']);
            if (!$service) {
                continue; // Ignorer les lignes avec des services non trouvés
            }


            LigneRendezvous::create([
                'rendezvous_id' => $this->id,
                'service_id' => $service->id,
            ]);
       
    }
    }


    // calculerTarifTotal(lignes:array) : float
    public function calculerTarifTotal(array $lignes): float
    {
        $total = 0;
        foreach ($lignes as $ligne) {
            $service = Service::find($ligne['service_id']);
            if (!$service) {
                continue; // Ignorer les lignes avec des services non trouvés
            }

            $total += $service->tarif;
        }
           $this->tarifTotal = $total;
        $this->save();
    
        return $total;
    }


}