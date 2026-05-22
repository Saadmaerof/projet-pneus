<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    //
    protected $fillable = [
        'client_id',
        'statut',
        'montant_total',
    ];

// Relation avec le client
      public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function rendezVous()
    {
      //return $this->belongsTo(RendezVous::class, 'rendez_vous_id');
    }

    public function lignesCommande()
    {
      return $this->hasMany(Ligne_commande::class, 'commande_id');
    }
public function pneus()
{
    return $this->belongsToMany(Pneu::class, 'ligne_commandes', 'commande_id', 'pneu_id')
                ->withPivot('quantite', 'prix_unitaire')
                ->withTimestamps();
}




//les méthodes métier
     // calculerMontantTotal(lignes:array) : float
    public function calculerMontantTotal(array $lignes): float
    {
        $total = 0;

        foreach ($lignes as $ligne) {
            $pneu = Pneu::find($ligne['pneu_id']);
            if (!$pneu) {
                continue; // Ignorer les lignes avec des pneus non trouvés
            }
            $prixUnitaire = $pneu->prix; // Utiliser le prix du pneu

            $total += $prixUnitaire * $ligne['quantite'];
        }

        $this->montant_total = $total;
        $this->save();

        return $total;
    }

      // ajouterLigneCommande(produit:Produit, quantite:int) : void
   public function ajouterLigneCommande(array $lignes): void
    {
        foreach ($lignes as $ligne) {
            $pneu = Pneu::find($ligne['pneu_id']);
            if (!$pneu) {
                continue; // Ignorer les lignes avec des pneus non trouvés
            }

            Ligne_commande::create([
                'commande_id'   => $this->id,
                'pneu_id'    => $pneu->id,
                'quantite'      => $ligne['quantite'],
            'prix_unitaire' => $pneu->prix,
        ]);

        
       
    }
}
}