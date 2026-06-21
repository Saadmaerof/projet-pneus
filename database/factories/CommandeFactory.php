<?php

namespace Database\Factories;

use App\Models\Commande;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    protected $model = Commande::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'date_commande' => $this->faker->dateTimeThisMonth(),
            'statut' => $this->faker->randomElement(['en attente', 'validee', 'livree', 'annulee']),
            'montant_total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
