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

}

