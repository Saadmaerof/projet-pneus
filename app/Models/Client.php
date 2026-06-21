<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   protected $table = 'clients';

    protected $fillable = [
        'telephone',
        'adresse',
        'statut',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function commandes()
    {
        // Ajuste 'Commande::class' selon le nom exact de ton modèle de commande
        return $this->hasMany(Commande::class); 
    }

}
