<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Администратор']);
        Role::create(['name' => 'Редактор']);
        Role::create(['name' => 'Игрок']);
        Role::create(['name' => 'Пользователь']);
    }
}
