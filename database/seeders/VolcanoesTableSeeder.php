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
            ],
            [
                'name' => 'Afderà',
                'country' => 'Ethiopia',
                'continent' => 'Africa',
                'activity' => 'Inactive',
                'latitude' => 13.08,
                'longitude' => 40.85,
                'elevation' => 1295,
                'description' => 'An isolated rhyolitic stratovolcano located at the intersection of three fault systems between the Erta Ale, Tat Ali, and Alayta mountain ranges in the Danakil Depression.',
                'type' => 'Stratovolcano',
                'image_url' => 'afdera'
            ],
            [
                'name' => 'Agrigan',
                'country' => 'Mariana Islands',
                'continent' => 'Asia',
                'activity' => 'Inactive',
                'latitude' => 18.77,
                'longitude' => 145.67,
                'elevation' => 965,
                'description' => 'The highest of the Marianas arc volcanoes, containing a 500-meter-deep flat-floored caldera. The elliptical island is 8 km long and its summit is the top of a massive 4000-meter-high submarine volcano. Last eruption was in 1917.',
                'type' => 'Stratovolcano',
                'image_url' => 'agrigan'
            ],
            [
                'name' => 'Agua',
                'country' => 'Guatemala',
                'continent' => 'North America',
                'activity' => 'Extinct',
                'latitude' => 14.47,
                'longitude' => -90.74,
                'elevation' => 3760,
                'description' => 'A symmetrical, forested stratovolcano that forms a prominent backdrop to Antigua Guatemala. Despite its youthful profile, it has had no historical eruptions but produced a devastating mudflow in 1541 that destroyed the first Guatemalan capital.',
                'type' => 'Stratovolcano',
                'image_url' => 'agua'
            ],
            [
                'name' => 'Agua de Pau',
                'country' => 'Portugal',
                'continent' => 'Europe',
                'activity' => 'Inactive',
                'latitude' => 37.77,
                'longitude' => -25.47,
                'elevation' => 947,
                'description' => 'A stratovolcanic complex located in the central part of São Miguel Island in the Azores, recognized for the Lagoa do Fogo caldera lake at its center. Last erupted in 1563-1564.',
                'type' => 'Stratovolcano',
                'image_url' => 'agua_de_pau'
            ],
            [
                'name' => 'Aguilera',
                'country' => 'Chile',
                'continent' => 'South America',
                'activity' => 'Inactive',
                'latitude' => -50.33,
                'longitude' => -73.75,
                'elevation' => 2546,
                'description' => 'A remote stratovolcano located in southern Chilean Patagonia, within the Southern Patagonian Ice Field. It is one of the most isolated volcanoes in the region.',
                'type' => 'Stratovolcano',
                'image_url' => 'aguilera'
            ],
            [
                'name' => 'Agung',
                'country' => 'Indonesia',
                'continent' => 'Asia',
                'activity' => 'Active',
                'latitude' => -8.34,
                'longitude' => 115.51,
                'elevation' => 3142,
                'description' => 'A sacred mountain in Bali and the highest point on the island. After 53 years of quiescence, it awoke in 2017 with intense seismicity and erupted in November 2017, continuing with smaller eruptions through 2019.',
                'type' => 'Stratovolcano',
                'image_url' => 'agung'
            ],
            [
                'name' => 'Ahyi',
                'country' => 'Mariana Islands',
                'continent' => 'Asia',
                'activity' => 'Active',
                'latitude' => 20.42,
                'longitude' => 145.03,
                'elevation' => -137,
                'description' => 'A large conical submarine volcano that rises to within 79 meters of the sea surface, located about 18 km southeast of Farallon de Pajaros. Experienced eruptions in 2001, 2014, and recent unrest in 2024-2025.',
                'type' => 'Submarine',
                'image_url' => 'ahyi'
            ],
            [
                'name' => 'Akademia Nauk',
                'country' => 'Russia',
                'continent' => 'Europe',
                'activity' => 'Inactive',
                'latitude' => 53.98,
                'longitude' => 159.45,
                'elevation' => 1180,
                'description' => 'A caldera volcano located on the Kamchatka Peninsula in the eastern part of Russia. Part of the volcanically active Pacific Ring of Fire region.',
                'type' => 'Caldera',
                'image_url' => 'akademia_nauk'
            ],
            [
                'name' => 'Akagi',
                'country' => 'Japan',
                'continent' => 'Asia',
                'activity' => 'Inactive',
                'latitude' => 36.56,
                'longitude' => 139.20,
                'elevation' => 1828,
                'description' => 'A stratovolcano located in Gunma Prefecture on Honshu Island, featuring a caldera lake called Lake Onuma. The volcano is considered dormant with no historical eruptions.',
                'type' => 'Stratovolcano',
                'image_url' => 'akagi'
            ],
            [
                'name' => 'Akan',
                'country' => 'Japan',
                'continent' => 'Asia',
                'activity' => 'Active',
                'latitude' => 43.38,
                'longitude' => 144.01,
                'elevation' => 1499,
                'description' => 'A caldera volcano in eastern Hokkaido with multiple crater lakes including Lake Akan. The volcanic complex includes several active vents and has experienced numerous small eruptions in recent history.',
                'type' => 'Caldera',
                'image_url' => 'akan'
            ],
            [
                'name' => 'Akhtang',
                'country' => 'Russia',
                'continent' => 'Europe',
                'activity' => 'Inactive',
                'latitude' => 55.43,
                'longitude' => 158.65,
                'elevation' => 1956,
                'description' => 'A shield volcano located on the Kamchatka Peninsula in eastern Russia. Part of the Kamchatka volcanic region with no recorded historical eruptions.',
                'type' => 'Shield',
                'image_url' => 'akhtang'
            ]
        ];
        
        foreach ($volcanoes as $volcano) {
            Volcano::create($volcano);
        }
    }
}
