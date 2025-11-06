<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Eruption',
                'description' => 'Visit your first volcano.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'images/badges/First Eruption.png',
            ],
            [
                'name' => 'Lava Rookie',
                'description' => 'Visit 5 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 5,
                'image_path' => 'images/badges/Lava Rookie.png',
            ],
            [
                'name' => 'Explorer',
                'description' => 'Visit one volcano in each continent.',
                'metric' => 'visits_by_continent',
                'dimensions' => json_encode(['continent' => ['Asia', 'Europe', 'Africa', 'North America', 'South America', 'Australia']]),
                'aggregator' => 'count_distinct',
                'threshold' => 6,
                'image_path' => 'images/badges/Explorer.png',
            ],
            [
                'name' => 'Dormant Dreamer',
                'description' => 'Visit an extinct volcano.',
                'metric' => 'visits_by_activity',
                'dimensions' => json_encode(['activity' => 'Extinct']),
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'images/badges/Dormant Dreamer.png',
            ],
            [
                'name' => 'Ash Walker',
                'description' => 'Visit 10 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 10,
                'image_path' => 'images/badges/Ash Walker.png',
            ],
            [
                'name' => 'Lava Lover',
                'description' => 'Visit an active volcano.',
                'metric' => 'visits_by_activity',
                'dimensions' => json_encode(['activity' => 'Active']),
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'images/badges/Lava Lover.png',
            ],
            [
                'name' => 'Volcano Veteran',
                'description' => 'Visit 25 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 25,
                'image_path' => 'images/badges/Volcano Veteran.png',
            ],
            [
                'name' => 'Magma Master',
                'description' => 'Visit 50 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 50,
                'image_path' => 'images/badges/Magma Master.png',
            ],

        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
