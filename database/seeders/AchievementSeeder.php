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
                'image_path' => 'First Eruption.png',
                'locked_image_path' => 'First Eruption Locked.png',
            ],
            [
                'name' => 'Lava Rookie',
                'description' => 'Visit 5 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 5,
                'image_path' => 'Lava Rookie.png',
                'locked_image_path' => 'Lava Rookie Locked.png',
            ],
            [
                'name' => 'Explorer',
                'description' => 'Visit one volcano in each continent.',
                'metric' => 'visits_by_continent',
                'dimensions' => json_encode(['continent' => ['Asia', 'Europe', 'Africa', 'North America', 'South America', 'Australia']]),
                'aggregator' => 'count_distinct',
                'threshold' => 6,
                'image_path' => 'Explorer.png',
                'locked_image_path' => 'Explorer Locked.png',
            ],
            [
                'name' => 'Dormant Dreamer',
                'description' => 'Visit an extinct volcano.',
                'metric' => 'visits_by_activity',
                'dimensions' => json_encode(['activity' => 'Extinct']),
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'Dormant Dreamer.png',
                'locked_image_path' => 'Dormant Dreamer Locked.png',
            ],
            [
                'name' => 'Ash Walker',
                'description' => 'Visit 10 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 10,
                'image_path' => 'Ash Walker.png',
                'locked_image_path' => 'Ash Walker Locked.png',
            ],
            [
                'name' => 'Lava Lover',
                'description' => 'Visit an active volcano.',
                'metric' => 'visits_by_activity',
                'dimensions' => json_encode(['activity' => 'Active']),
                'aggregator' => 'count',
                'threshold' => 1,
                'image_path' => 'Lava Lover.png',
                'locked_image_path' => 'Lava Lover Locked.png',
            ],
            [
                'name' => 'Volcano Veteran',
                'description' => 'Visit 25 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 25,
                'image_path' => 'Volcano Veteran.png',
                'locked_image_path' => 'Volcano Veteran Locked.png',
            ],
            [
                'name' => 'Magma Master',
                'description' => 'Visit 50 volcanoes.',
                'metric' => 'total_visits',
                'dimensions' => null,
                'aggregator' => 'count',
                'threshold' => 50,
                'image_path' => 'Magma Master.png',
                'locked_image_path' => 'Magma Master locked.png',
            ],

        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
