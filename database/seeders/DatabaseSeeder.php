<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(ThemesTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        
        User::create(['login' => 'admin', 
        'email' => 'admin@ex.ex',
        'password' => 'admin',
        'role_id' => 1]);

        // User::factory(20)->create();

    }
}
