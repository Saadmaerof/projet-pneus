<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class Serviceseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['nom' => 'montage', 'description' => 'Service de changement de pneu pour tous types de véhicules.'],
            ['nom' => 'équilibrage', 'description' => 'Service d\'équilibrage des roues pour une conduite plus stable et confortable.'],
            ['nom' => 'réparation', 'description' => 'Service de réparation de pneu pour les crevaisons et autres dommages mineurs.'],
        ];

        foreach ($services as $service) {
           Service::create($service);
        }
    }
}
