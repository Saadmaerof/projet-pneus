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
        return $this->belongsTo(User::class);
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

    





}

