<?php

namespace Database\Factories;

use App\Models\Pneu;
use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pneu>
 */
class PneuFactory extends Factory
{
    protected $model = Pneu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marque' => $this->faker->randomElement(['Michelin', 'Bridgestone', 'Goodyear', 'Pirelli', 'Continental']),
            'modele' => $this->faker->word(),
            'prix' => $this->faker->randomFloat(2, 50, 300),
            'stock' => $this->faker->numberBetween(0, 100),
            'vehicule_id' => Vehicule::inRandomOrder()->first() ?? Vehicule::factory(),
        ];
    }
}
