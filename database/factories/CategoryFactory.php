<?php

// database/factories/CategoryFactory.php
namespace Database\Factories;

use App\Models\Category; // Asegúrate de que el namespace de tu modelo sea correcto
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->words(2, true); // Genera 2 palabras únicas
        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'parent_id' => null,
        ];
    }

    public function subcategory()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Category::inRandomOrder()->first()?->id ?? null,
            ];
        });
    }
}
