<?php

namespace Database\Factories;

use App\Models\SeoRedirect;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeoRedirect>
 */
class SeoRedirectFactory extends Factory
{
    protected $model = SeoRedirect::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $from = 'old-'.fake()->unique()->slug(2);
        $to = 'new-'.fake()->slug(2);

        return [
            'slug_from' => $from,
            'slug_to' => $to,
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
