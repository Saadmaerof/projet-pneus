<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'telephone' => $this->faker->phoneNumber(),
            'adresse' => $this->faker->address(),
            'statut' => $this->faker->randomElement(['actif', 'bloque']),
            'user_id' => User::factory(),
        ];
    }
}
