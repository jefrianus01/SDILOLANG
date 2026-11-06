<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'caption' => fake()->sentence(10),
            'image_path' => 'gallery/demo-1.svg',
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }
}
