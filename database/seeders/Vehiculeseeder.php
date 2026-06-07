<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

 use App\Models\Vehicule;
class Vehiculeseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicules = [
            [
                'vehicule' => 'Voiture',
                'description' => 'Pneus pour voitures particulières (berlines, compactes, citadines). Alliant confort acoustique, faible consommation et longévité. Disponibles en été, hiver ou 4 saisons. Technologies silice pour adhérence sur mouillé et sculptures anti-bruit.',
                'image' => 'categorie_vehicules/pneu_tourisme_1.jpg',
            ],
            [
                'vehicule' => 'Sport Utility Vehicle/4x4',
                'description' => 'Pneus renforcés pour SUV, crossovers et 4x4. Résistance aux chocs et crevaisons. Sculptures mixte (route/chemin) ou Mud-Terrain. Flancs protégés et marquage XL (extra load). Stabilité en virage et freinage optimisés.',
                 'image' => 'categorie_vehicules/pneu_suv_1.jpg',
            ],
             [
                'vehicule' => 'Utilitaire léger',
                'description' => 'Pneus marqués \'C\' (cargo) pour fourgons, camionnettes et pick-ups. Structure renforcée, haute pression (jusqu\'à 4,5 bars), indice de charge élevé. Bande de roulement résistante à l\'abrasion pour kilométrage accru et stabilité sous charge. Idéal pour usage professionnel et transport de marchandises.',
                'image' => 'categorie_vehicules/pneu_utilitaire_1.jpg',
            ],
            [
                'vehicule' => 'Poids lourds',
                'description' => 'Pneus pour camions, bus, semi-remorques. Structure radiale supportant de fortes charges. Rechapables (plusieurs vies). Sculptures selon essieu : direction, tracteur ou remorque. Gommes basse consommation (label EU).',
                'image' => 'categorie_vehicules/pneu_poids_lourds_1.jpg',
            ],



 [
                'vehicule' => 'Moto/scooter',
                'description' => 'Pneus pour deux-roues motorisés (scooters, motos roadster, sportives, trails). Profil arrondi pour l\'inclinaison. Gomme tendre (adhérence) ou dure (endurance). Bi-composé pour usage sportif. Indices de vitesse S/H/V/W.',
                'image' => 'categorie_vehicules/pneu_moto_1.jpg',
            ],

 [
                'vehicule' => 'Agricole',
                'description' => 'Pneus pour tracteurs, moissonneuses. Sculptures profondes en chevrons pour motricité et moindre tassement du sol. Flancs souples, gonflage centralisé. Compatibles route et champ.',
                'image' => 'categorie_vehicules/pneu_agricole_1.jpg',
            ],
 

        ];


        foreach ($vehicules as $vehicule) {
             
                Vehicule::create([
                    'vehicule'    => $vehicule['vehicule'],
                    'description' => $vehicule['description'],
                    'image'       => $vehicule['image']
                ]);
      
        }

    


    }
}
