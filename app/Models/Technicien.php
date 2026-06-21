<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technicien extends Model
{
    protected $table = 'techniciens';

    protected $guarded = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function rendezvous()
    {
        return $this->belongsToMany(Rendezvous::class, 'ligne_rendezvous')
            ->withPivot('service_id')
            ->withTimestamps();
                  
    }
    public function ligne_rendezvous()
    {
        return $this->hasMany(LigneRendezvous::class);
    }

    public function servicesbyrendezvous()
    {
        return $this->hasMany(Service::class, 'ligne_rendezvous')
            ->withPivot('rendezvous_id')
            ->withTimestamps();
    }
    public function service__technicien()
    {
        return $this->hasMany(Service_Technicien::class);
    }
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service__technicien', 'technicien_id', 'service_id')
                    ->withTimestamps();
    }

    //les methodes

   public function ajouterService_Technicien(array $lignes): void
{
    foreach ($lignes as $ligne) {
        if (!isset($ligne['service_id'])) {
            continue;
        }

        $service = Service::find($ligne['service_id']);
        if (!$service) {
            continue; // Ignore si l'ID du service n'existe pas en BDD
        }

        Service_Technicien::create([
            'technicien_id' => $this->id,
            'service_id'    => $service->id,
        ]);
    }
}
    
}
    







