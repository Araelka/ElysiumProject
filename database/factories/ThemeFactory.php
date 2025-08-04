<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theme>
 */
class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Theme::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Theme $theme) {
            // Создаем связанную статью
            $theme->article()->create([
                'content' => '',
            ]);
        });
    }
}
