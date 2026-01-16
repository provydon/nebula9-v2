<?php

namespace Database\Seeders;

use App\Models\Enclosure;
use Illuminate\Database\Seeder;

class EnclosureSeeder extends Seeder
{
    public function run(): void
    {
        $enclosures = [
            [
                'name' => 'Volcanic Crater Zone',
                'type' => 'Volcanic',
                'capacity' => 5,
            ],
            [
                'name' => 'Ice Dome Alpha',
                'type' => 'Tundra',
                'capacity' => 8,
            ],
            [
                'name' => 'Ice Dome Beta',
                'type' => 'Tundra',
                'capacity' => 3,
            ],
            [
                'name' => 'Jungle Habitat',
                'type' => 'Jungle',
                'capacity' => 10,
            ],
            [
                'name' => 'Hot Springs Enclosure',
                'type' => 'Hot',
                'capacity' => 4,
            ],
            [
                'name' => 'Savanna Plains',
                'type' => 'Savanna',
                'capacity' => 12,
            ],
            [
                'name' => 'Aquatic Tank',
                'type' => 'Aquatic',
                'capacity' => 15,
            ],
            [
                'name' => 'Desert Dunes',
                'type' => 'Desert',
                'capacity' => 6,
            ],
        ];

        foreach ($enclosures as $enclosure) {
            Enclosure::create($enclosure);
        }
    }
}
