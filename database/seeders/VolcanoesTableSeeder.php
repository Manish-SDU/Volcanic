<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Volcano;
use Illuminate\Support\Facades\DB;

class VolcanoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    /**
     * 
     * === GET THE MISSING FILES TO RUN THE LARAVEL APP WITH COMPOSER ===
     * 
     *  1. composer install
     *  2. cp .env.example .env
     *  3. php artisan key:generate
     *  4. php artisan migrate
     */

    public function run(): void
    {
        // Clear existing volcanoes
        DB::table('volcanoes')->truncate();
        
        // ⚠️ Remember, to refresh the data you must: "php artisan migrate:fresh --seed"

        // Add real volcano data
        $volcanoes = [
            [
                'name' => 'Abu',
                'country' => 'Japan',
                'continent' => 'Asia',
                'activity' => 'Inactive', // Active, Inactive, Extinct
                'latitude' => 34.50,
                'longitude' => 131.60,
                'elevation' => 641,
                'description' => 'A group of shield volcanoes located on the southwest end of Honshu Island, with the highest peak being Irao-yama.',
                'type' => 'Shield',
                'image_url' => 'abu'
            ],
            [
                'name' => 'Acamarachi',
                'country' => 'Chile',
                'continent' => 'South America',
                'activity' => 'Extinct',
                'latitude' => -23.30,
                'longitude' => -67.62,
                'elevation' => 6046,
                'description' => 'A steep, cone-shaped stratovolcano in the Andes, with evidence of postglacial lava flows and an Incan sanctuary.',
                'type' => 'Stratovolcano',
                'image_url' => 'acamarachi'
            ],
            [
                'name' => 'Acatenango',
                'country' => 'Guatemala',
                'continent' => 'North America',
                'activity' => 'Inactive',
                'latitude' => 14.50,
                'longitude' => -90.88,
                'elevation' => 3976,
                'description' => 'A prominent stratovolcano near Antigua, Guatemala, with a history of explosive eruptions and significant pyroclastic flows.',
                'type' => 'Stratovolcano',
                'image_url' => 'acatenango'
            ],
            [
                'name' => 'Acıgöl-Nevşehir',
                'country' => 'Turkey',
                'continent' => 'Asia',
                'activity' => 'Inactive',
                'latitude' => 38.537,
                'longitude' => 34.621,
                'elevation' => 1683,
                'description' => 'A late Pleistocene caldera (~7 × 8 km), with post-caldera maars, lava domes, pyroclastic cones.',
                'type' => 'Caldera',
                'image_url' => 'acigöl-nevsehir'
            ],
            [
                'name' => 'Mount Adams',
                'country' => 'United States',
                'continent' => 'North America',
                'activity' => 'Inactive',
                'latitude' => 46.21,
                'longitude' => -121.49,
                'elevation' => 3742,
                'description' => 'A large stratovolcano in the Cascades. Last significant eruptions are in the geologic past (~ thousands to tens of thousands of years ago).',
                'type' => 'Stratovolcano',
                'image_url' => 'adams'
            ],
            [
                'name' => 'Adams Seamount',
                'country' => 'Pacific Ocean',
                'continent' => '— (oceanic)',
                'activity' => 'Active',
                'latitude' => -25.37,
                'longitude' => -129.27,
                'elevation' => -39,
                'description' => 'A submarine volcano rising ~3,500 m above the seafloor to within ~39 m of the surface. No confirmed historic eruptions in very recent times, but geologic data indicates relatively recent activity.',
                'type' => 'Submarine volcano',
                'image_url' => 'adams-seamount'
            ],
            [
                'name' => 'Adatara',
                'country' => 'Japan',
                'continent' => 'Asia',
                'activity' => 'Active',
                'latitude' => 37.64,
                'longitude' => 140.29,
                'elevation' => 1728,
                'description' => 'A complex of overlapping stratovolcanoes east of Bandai volcano. The latest eruptions are from the Numano-daira crater. (volcano.si.edu)',
                'type' => 'Stratovolcano',
                'image_url' => 'adatara'
            ],
            [
                'name' => 'Adwa',
                'country' => 'Ethiopia',
                'continent' => 'Africa',
                'activity' => 'Inactive',
                'latitude' => 10.07,
                'longitude' => 40.84,
                'elevation' => 1733,
                'description' => 'A stratovolcano with a 4 × 5 km caldera, young basaltic flows, and fumarolic activity. Uncertain historical eruptions possibly in 1828 or 1928.',
                'type' => 'Stratovolcano',
                'image_url' => 'adwa'
            ]
        ];
        
        foreach ($volcanoes as $volcano) {
            Volcano::create($volcano);
        }
    }
}
