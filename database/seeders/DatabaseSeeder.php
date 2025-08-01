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
        
        $user = User::create([
            'login' => 'admin', 
            'email' => 'admin@ex.ex',
            'password' => 'admin',
        ]);

        $user->roles()->attach(1);

        $this->call(CharacterStatusTableSeeder::class);
        $this->call(AttributeTableSeeder::class);
        $this->call(SkillTableSeeder::class);

        // User::factory(20)->create();

    }
}
