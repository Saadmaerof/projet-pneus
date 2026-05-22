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
}
