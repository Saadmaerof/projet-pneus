<?php

namespace Database\Factories;

use App\Models\Ligne_commande;
use App\Models\Commande;
use App\Models\Pneu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ligne_commande>
 */
class LigneCommandeFactory extends Factory
{
    protected $model = Ligne_commande::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commande_id' => Commande::factory(),
            'pneu_id' => Pneu::inRandomOrder()->first() ?? Pneu::factory(),
            'quantite' => $this->faker->numberBetween(1, 5),
            'prix_unitaire' => $this->faker->randomFloat(2, 50, 300),
        ];
    }
}
