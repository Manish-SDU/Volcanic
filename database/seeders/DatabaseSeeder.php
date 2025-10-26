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
            'username'      => 'MarioRossi',
            'date_of_birth' => '2000-01-01',
            'where_from'    => 'Italy',
            'bio'           => 'Volcano enthusiast.',
            'password'      => Hash::make('password123'), 
        ]);
            
        // Run the volcano seeder
        $this->call([
            VolcanoesTableSeeder::class,
        ]);
    }
}
