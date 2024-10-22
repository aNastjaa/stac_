<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Upload>
 */
class UploadFactory extends Factory
{
    protected $model = \App\Models\Upload::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(), // Generating a UUID for the ID field
            'file_url' => $this->faker->imageUrl(), // Generate a random image URL
            'file_type' => $this->faker->randomElement(['avatar', 'brand_logo']), // Randomly choose between 'avatar' and 'brand_logo'
            'created_at' => now(), // Current timestamp
            'updated_at' => now(), // Current timestamp
        ];
    }
}
