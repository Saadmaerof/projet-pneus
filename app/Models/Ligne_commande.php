<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ligne_commande extends Model
{
 protected $table = 'ligne_commandes';

    protected $fillable = [
        'commande_id',
        'pneu_id',
        'quantite',
        'prix_unitaire',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function pneu()
    {
        return $this->belongsTo(Pneu::class);
    }

    //les méthodes métier
    //calculerTotal() : float
    public function calculerTotal(): float
    {
        return $this->quantite * $this->prix_unitaire;
    }



}
