<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pneu extends Model
{

protected $guarded = ['id','updated_at','	created_at'];


protected function vehicule(){

return $this->belongsTo(Vehicule::class);

}
}