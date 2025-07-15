<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::create(['name' => 'Интелект']);
        Attribute::create(['name' => 'Психика']);
        Attribute::create(['name' => 'Физиология']);
        Attribute::create(['name' => 'Моторика']);
    }
}
