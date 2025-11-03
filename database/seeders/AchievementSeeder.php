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
                'description' => 'Visit your First Volcano.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'images/badges/First Eruption.png',
            ],
            [
                'name' => 'Lava Rookie',
                'description' => 'Visit 5 Volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 5,
                'image_path' => 'images/badges/Lava Rookie.png',
            ],
            [
                'name' => 'Explorer',
                'description' => 'Visit one Volcano in each continent.',
                'metric' => 'visits_by_continent',
                'dimensions' => null,
                'aggregator' => 'count_distinct',
                'threshold' => 6,
                'image_path' => 'images/badges/Explorer.png',
            ],
            [
                'name' => 'Dormant Dreamer',
                'description' => 'Visit an extinct volcano.',
                'metric' => 'visits_by_activity',
                'dimensions' => ['activity' => 'Extinct'],
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'images/badges/Dormant Dreamer.png',
            ]
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
