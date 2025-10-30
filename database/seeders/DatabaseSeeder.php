<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserVolcano;
use App\Models\Volcano;
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
            'password'      => Hash::make('admin123'), 
            'is_admin'      => true,
        ]);

        User::create([
            'name'          => 'Mario',
            'surname'       => 'Rossi',
            'username'      => 'MaRoss777',
            'date_of_birth' => '2000-01-01',
            'where_from'    => 'Italy',
            'bio'           => 'I rate volcanoes the way sommeliers rate wine. 🌋🇮🇹',
            'password'      => Hash::make('password123'),
            'is_admin'      => false, // No admin role for regular user check
        ]);
            
        // Run the volcano seeder
        $this->call([
            VolcanoesTableSeeder::class,
        ]);
    }
}
