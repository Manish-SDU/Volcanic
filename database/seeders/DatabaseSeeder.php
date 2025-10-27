<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name'          => 'Mario',
            'surname'       => 'Rossi',
            'username'      => 'MaRoss777',
            'date_of_birth' => '2000-01-01',
            'where_from'    => 'Italy',
            'bio'           => 'Born between a cup of espresso and a plate of carbonara. 🍝☕
                                I rate volcanoes the way sommeliers rate wine — by aroma, heat, and aftertaste.
                                If it rumbles, I’m probably already there with my moka pot. 🌋🇮🇹',
            'password'      => Hash::make('password123'), 
        ]);
            
        // Run the volcano seeder
        $this->call([
            VolcanoesTableSeeder::class,
        ]);
    }
}
