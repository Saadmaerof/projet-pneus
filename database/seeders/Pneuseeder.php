<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pneu;

class Pneuseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  
  public function run(): void
    {
        $marques = [
            'Michelin',
            'Pirelli',
            'Bridgestone',
            'Continental',
            'Goodyear',
            'Hankook',
            'Dunlop',
            'Yokohama',
            'Firestone',
            'Toyo'
        ];

        $modeles = [
            'Primacy',
            'Pilot Sport',
            'Energy Saver',
            'Scorpion',
            'Turanza',
            'Eagle F1',
            'Ventus',
            'BluEarth',
            'Open Country',
            'WinterContact'
        ];

        $saisons = [
            'Été',
            'Hiver',
            '4 saisons'
        ];

        $indicesVitesse = [
            'H',
            'V',
            'W',
            'Y',
            'T'
        ];

        $descriptions = [
            'Pneu haute performance offrant une excellente adhérence et une longue durée de vie.',
            'Pneu conçu pour améliorer le confort de conduite et réduire la consommation.',
            'Pneu idéal pour les routes mouillées avec une excellente stabilité.',
            'Pneu renforcé adapté aux longues distances et aux charges élevées.',
            'Pneu performant offrant sécurité et précision dans les virages.'
        ];

        for ($i = 1; $i <= 200; $i++) {

            Pneu::create([
                'marque'          => $marques[array_rand($marques)],
                'modele'          => $modeles[array_rand($modeles)] . ' ' . rand(1, 9),
                'largeur'         => [185, 195, 205, 215, 225, 235][array_rand([185,195,205,215,225,235])],
                'hauteur'         => [45, 50, 55, 60, 65][array_rand([45,50,55,60,65])],
                'diametre_pouces' => [15, 16, 17, 18, 19][array_rand([15,16,17,18,19])],
                'saison'          => $saisons[array_rand($saisons)],
                'indice_charge'   => rand(75, 110),
                'indice_vitesse'  => $indicesVitesse[array_rand($indicesVitesse)],
                'prix'            => rand(500, 3500),
                'description'     => $descriptions[array_rand($descriptions)],
                'quantite'        => rand(0, 100),

                // image exemple
                'image'           => 'storage/app/public/pneus/pneu_' . rand(1, 10) . '.jpg',

                // IDs des catégories véhicules (1 → 6)
                'vehicule_id'     => rand(1, 6),
            ]);
        }
    }


}
