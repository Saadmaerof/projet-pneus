<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    //
    protected $fillable = ['vehicule', 'description', 'image'];

    public function pneus(){

return $this->hasMany(Pneu::class);

}



}
