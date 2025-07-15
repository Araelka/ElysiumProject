<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Skill::create(['attribute_id' => 1, 'name' => 'Логика']); 
        Skill::create(['attribute_id' => 2, 'name' => 'Сила воли']);
        Skill::create(['attribute_id' => 3, 'name' => 'Стойкость']);
        Skill::create(['attribute_id' => 4, 'name' => 'Координация']);
    }
}
