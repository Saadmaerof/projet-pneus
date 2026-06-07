<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneRendezvous extends Model
{
        protected $table = 'ligne_rendezvous';

        protected $guarded = [
            'id'
        ];

        public function rendezvous()
        {
            return $this->belongsTo(Rendezvous::class, 'rendezvous_id');
        }

        public function service()
        {
            return $this->belongsTo(Service::class, 'service_id');
        }

        public function technicien()
        {
            return $this->belongsTo(Technicien::class, 'technicien_id');
        }
}
