<?php

namespace Database\Factories;

use App\Models\LigneRendezvous;
use App\Models\Rendezvous;
use App\Models\Service;
use App\Models\Technicien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LigneRendezvous>
 */
class LigneRendezvousFactory extends Factory
{
    protected $model = LigneRendezvous::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rendezvous_id' => Rendezvous::inRandomOrder()->first() ?? Rendezvous::factory(),
            'service_id' => Service::inRandomOrder()->first() ?? Service::factory(),
            'technicien_id' => Technicien::inRandomOrder()->first(),
            'duree' => $this->faker->numberBetween(30, 180),
            'tarif' => $this->faker->randomFloat(2, 50, 200),
            'statut' => $this->faker->randomElement(['en attente', 'en cours', 'terminé', 'annulé']),
        ];
    }
}
