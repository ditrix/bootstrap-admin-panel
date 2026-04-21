<?php

namespace Database\Factories;

use App\Models\CategoryTree;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryTree>
 */
class CategoryTreeFactory extends Factory
{
    protected $model = CategoryTree::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sortCounter = [];

        $parentId = 0;
        $sortCounter[$parentId] = ($sortCounter[$parentId] ?? 0);
        $sortNo = $sortCounter[$parentId]++;

        return [
            'parent_id' => $parentId,
            'sort_no' => $sortNo,
            'title' => fake()->words(fake()->numberBetween(2, 4), true),
            'is_active' => fake()->boolean(80),
        ];
    }

    public function childOf(CategoryTree $parent, int $sortNo = 0): self
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->getKey(),
            'sort_no' => $sortNo,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
