<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'name_ar' => fake()->words(2, true),
            'slug' => Str::slug($name),
            'image' => null,
            'parent_id' => null,
        ];
    }

    public function child(Category $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
        ]);
    }
}
