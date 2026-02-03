<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserVolcano;
use App\Models\Volcano;
use App\Models\Achievement;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Adding the admin features, same as the user bbelow but check if admin role
        User::create([
            'name'          => 'Admin',
            'surname'       => 'User',
            'username'      => 'admin',
            'date_of_birth' => '2005-04-27',
            'where_from'    => 'VolcanoLandia',
            'bio'           => 'Im the bossy boss 🌋',
            'password'      => Hash::make('Volcanic!Demo#2026'), 
            'is_admin'      => true,
        ]);

        User::create([
            'name'          => 'Mario',
            'surname'       => 'Rossi',
            'username'      => 'MarioR',
            'date_of_birth' => '2000-01-01',
            'where_from'    => 'Italy',
            'bio'           => 'I rate volcanoes the way sommeliers rate wine. 🌋🇮🇹',
            'password'      => Hash::make('Volcanic!User#2026'),
            'is_admin'      => false, // No admin role for regular user check
        ]);

        User::create([
            'name'          => 'deleteme',
            'surname'       => 'NA',
            'username'      => 'deleteme',
            'date_of_birth' => '2000-01-01',
            'where_from'    => 'France',
            'bio'           => 'Im just a test',
            'password'      => Hash::make('Volcanic!Test#2026'),
            'is_admin'      => false,
        ]);
            
        // Run the volcano and achievements seeders
        $this->call([
            VolcanoesTableSeeder::class,
            AchievementSeeder::class,
        ]);
    }
}
