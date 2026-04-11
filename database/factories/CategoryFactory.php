<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    private const CATEGORIE = [
        ['name' => 'Corpo Europeo Solidarietà', 'tag' => 'CES'],
        ['name' => 'Scambi Giovanili',         'tag' => 'SG'],
        ['name' => 'Corsi Formazione',          'tag' => 'CF'],
    ];

    public function definition(): array
    {
        return $this->faker->randomElement(self::CATEGORIE);
    }
}
