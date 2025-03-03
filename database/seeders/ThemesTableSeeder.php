<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThemesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create(['name' => 'Первая локация']);
        Location::create(['name' => 'Вторая локация']);
    }
}
