<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service_Technicien extends Model
{
    protected $table = 'service__technicien';





    protected $fillable = [
        'technicien_id',
        'service_id',
    ];
//
}
