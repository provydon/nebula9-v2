<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        $volcanicEnclosure = Enclosure::where('type', 'Volcanic')->first();
        $iceDome = Enclosure::where('name', 'Ice Dome Alpha')->first();
        $jungleHabitat = Enclosure::where('type', 'Jungle')->first();
        $hotSprings = Enclosure::where('type', 'Hot')->first();
        $savanna = Enclosure::where('type', 'Savanna')->first();

        $animals = [
            [
                'name' => 'Ignis',
                'specie' => 'Magma-Crab',
                'preferred_environment' => 'Hot',
                'enclosure_id' => $hotSprings?->id,
            ],
            [
                'name' => 'Blaze',
                'specie' => 'Magma-Crab',
                'preferred_environment' => 'Hot',
                'enclosure_id' => $hotSprings?->id,
            ],
            [
                'name' => 'Pyro',
                'specie' => 'Lava-Lizard',
                'preferred_environment' => 'Volcanic',
                'enclosure_id' => $volcanicEnclosure?->id,
            ],
            [
                'name' => 'Frostbite',
                'specie' => 'Ice-Wyrm',
                'preferred_environment' => 'Tundra',
                'enclosure_id' => $iceDome?->id,
            ],
            [
                'name' => 'Glacier',
                'specie' => 'Frost-Bear',
                'preferred_environment' => 'Tundra',
                'enclosure_id' => $iceDome?->id,
            ],
            [
                'name' => 'Rex',
                'specie' => 'T-Rex',
                'preferred_environment' => 'Jungle',
                'enclosure_id' => $jungleHabitat?->id,
            ],
            [
                'name' => 'Simba',
                'specie' => 'Space-Lion',
                'preferred_environment' => 'Savanna',
                'enclosure_id' => $savanna?->id,
            ],
            [
                'name' => 'Nala',
                'specie' => 'Space-Lion',
                'preferred_environment' => 'Savanna',
                'enclosure_id' => $savanna?->id,
            ],
            [
                'name' => 'Raptor',
                'specie' => 'Velociraptor',
                'preferred_environment' => 'Jungle',
                'enclosure_id' => $jungleHabitat?->id,
            ],
            [
                'name' => 'Ember',
                'specie' => 'Phoenix',
                'preferred_environment' => 'Volcanic',
                'enclosure_id' => $volcanicEnclosure?->id,
            ],
            [
                'name' => 'Chill',
                'specie' => 'Penguin-Beast',
                'preferred_environment' => 'Tundra',
                'enclosure_id' => null,
            ],
            [
                'name' => 'Spike',
                'specie' => 'Triceratops',
                'preferred_environment' => 'Jungle',
                'enclosure_id' => null,
            ],
        ];

        foreach ($animals as $animal) {
            Animal::create($animal);
        }
    }
}
