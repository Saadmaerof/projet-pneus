<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'nom',
        'description',
        'prix',
    ];

    public function ligne_rendezvous()
    {
        return $this->hasMany(LigneRendezvous::class);
    }

    public function service__technicien()
    {
        return $this->hasMany(Service_Technicien::class);
    }

    public function techniciens()
    {
        return $this->belongsToMany(Technicien::class, 'service__technicien', 'service_id', 'technicien_id')
                    ->withTimestamps();
    }
    public function rendezvous()
    {
        return $this->belongsToMany(Rendezvous::class, 'ligne_rendezvous', 'service_id', 'rendezvous_id')
                    ->withPivot('technicien_id')
                    ->withTimestamps();
    }
    
}
