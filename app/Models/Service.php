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
    
}
