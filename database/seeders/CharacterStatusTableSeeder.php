<?php

namespace Database\Seeders;

use App\Models\CharacterStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharacterStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CharacterStatus::create(['name' => 'В работе']);
        CharacterStatus::create(['name' => 'На рассмотрении']);
        CharacterStatus::create(['name' => 'Одобрен']);
        CharacterStatus::create(['name' => 'Отклонён']);
        CharacterStatus::create(['name' => 'В архиве']);
    }
}
