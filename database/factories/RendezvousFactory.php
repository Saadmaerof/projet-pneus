<?php

namespace Database\Factories;

use App\Models\Rendezvous;
use App\Models\Client;
use App\Models\Vehicule;
use App\Models\Commande;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rendezvous>
 */
class RendezvousFactory extends Factory
{
    protected $model = Rendezvous::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::inRandomOrder()->first() ?? Client::factory(),
            'vehicule_id' => Vehicule::inRandomOrder()->first() ?? Vehicule::factory(),
            'description' => $this->faker->sentence(),
            'commande_id' => Commande::inRandomOrder()->first(),
            'date' => $this->faker->dateTimeThisMonth(),
            'heure' => $this->faker->time('H:i'),
            'tarifTotal' => $this->faker->randomFloat(2, 100, 500),
            'statut' => $this->faker->randomElement(['en attente', 'validé', 'en cours', 'terminé', 'annulé']),
        ];
    }
}
