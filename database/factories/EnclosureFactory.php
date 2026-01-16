<?php

namespace Database\Factories;

use App\Models\Enclosure;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnclosureFactory extends Factory
{
    protected $model = Enclosure::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Enclosure',
            'type' => fake()->randomElement(['Savannah', 'Forest', 'Aquatic', 'Desert']),
            'capacity' => fake()->numberBetween(5, 50),
        ];
    }
}
