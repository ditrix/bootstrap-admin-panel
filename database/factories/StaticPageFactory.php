<?php

namespace Database\Factories;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StaticPage>
 */
class StaticPageFactory extends Factory
{
    protected $model = StaticPage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'parent_id' => 0,
            'code' => strtoupper(Str::random(8)),
            'title' => $title,
            'description' => fake()->optional()->paragraph(),
            'content' => fake()->optional(0.9)->paragraphs(3, true),
            'sort_no' => fake()->numberBetween(0, 1000),
            'slug' => Str::slug($title.'-'.Str::random(6)),
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function childOf(StaticPage $parent): self
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->getKey(),
        ]);
    }
}
