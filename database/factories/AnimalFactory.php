<?php

namespace Database\Factories;

use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'specie' => fake()->words(2, true),
            'preferred_environment' => fake()->randomElement(['Savannah', 'Forest', 'Aquatic', 'Desert']),
            'enclosure_id' => null,
        ];
    }
}
