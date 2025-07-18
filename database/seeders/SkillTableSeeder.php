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
        Skill::create(['attribute_id' => 1, 'name' => 'Логика', 'image_path'=> 'images/skills/Logic.webp']); 
        Skill::create(['attribute_id' => 1, 'name' => 'Энциклопедия', 'image_path'=> 'images/skills/Encyclopedia.webp']); 
        Skill::create(['attribute_id' => 1, 'name' => 'Риторика', 'image_path'=> 'images/skills/Rhetoric.webp']); 
        Skill::create(['attribute_id' => 1, 'name' => 'Драма', 'image_path'=> 'images/skills/Drama.webp']); 
        Skill::create(['attribute_id' => 1, 'name' => 'Концептуализация', 'image_path'=> 'images/skills/Conceptualization.webp']); 
        Skill::create(['attribute_id' => 1, 'name' => 'Визуальный анализ', 'image_path'=> 'images/skills/Visual-calculus.webp']); 

        Skill::create(['attribute_id' => 2, 'name' => 'Сила воли', 'image_path'=> 'images/skills/Volition.webp']);
        Skill::create(['attribute_id' => 2, 'name' => 'Внутренняя империя', 'image_path'=> 'images/skills/Inland-empire.webp']);
        Skill::create(['attribute_id' => 2, 'name' => 'Эмпатия', 'image_path'=> 'images/skills/Empathy.webp']);
        Skill::create(['attribute_id' => 2, 'name' => 'Авторитет', 'image_path'=> 'images/skills/Authority.webp']);
        Skill::create(['attribute_id' => 2, 'name' => 'Командный дух', 'image_path'=> 'images/skills/Esprit-de-Corps.webp']);
        Skill::create(['attribute_id' => 2, 'name' => 'Внушение', 'image_path'=> 'images/skills/Suggestion.webp']);

        Skill::create(['attribute_id' => 3, 'name' => 'Стойкость', 'image_path'=> 'images/skills/Endurance.webp']);
        Skill::create(['attribute_id' => 3, 'name' => 'Болевой порог', 'image_path'=> 'images/skills/Pain-threshold.webp']);
        Skill::create(['attribute_id' => 3, 'name' => 'Физический аппарат', 'image_path'=> 'images/skills/Physical-instrument.webp']);
        Skill::create(['attribute_id' => 3, 'name' => 'Электрохимия', 'image_path'=> 'images/skills/Electrochemistry.webp']);
        Skill::create(['attribute_id' => 3, 'name' => 'Трепет', 'image_path'=> 'images/skills/Shivers.webp']);
        Skill::create(['attribute_id' => 3, 'name' => 'Сумрак', 'image_path'=> 'images/skills/Half-light.webp']);

        Skill::create(['attribute_id' => 4, 'name' => 'Координация', 'image_path'=> 'images/skills/Coordination.webp']);
        Skill::create(['attribute_id' => 4, 'name' => 'Восприятие', 'image_path'=> 'images/skills/Perception.webp']);
        Skill::create(['attribute_id' => 4, 'name' => 'Скорость реакции', 'image_path'=> 'images/skills/Reaction.webp']);
        Skill::create(['attribute_id' => 4, 'name' => 'Эквилибристика', 'image_path'=> 'images/skills/Savoir-faire.webp']);
        Skill::create(['attribute_id' => 4, 'name' => 'Техника', 'image_path'=> 'images/skills/Interfacing.webp']);
        Skill::create(['attribute_id' => 4, 'name' => 'Самообладание', 'image_path'=> 'images/skills/Composure.webp']);
    }
}
