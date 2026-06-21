<?php

namespace Database\Factories;

use App\Models\Technicien;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Technicien>
 */
class TechnicienFactory extends Factory
{
    protected $model = Technicien::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'disponibilite' => $this->faker->boolean(80),
            'statut' => $this->faker->randomElement(['actif', 'bloque']),
            'users_id' => User::factory(),
        ];
    }
}
