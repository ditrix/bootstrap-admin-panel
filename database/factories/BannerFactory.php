<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('bnr_???###'),
            'title' => fake()->sentence(3),
            'sort_no' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'image_path' => null,
        ];
    }
}
