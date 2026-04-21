<?php

namespace App\Http\Requests\Admin\CategoryTree;

use App\Models\CategoryTree;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCategoryTreeOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nodes' => [
                'required',
                'array',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $this->allIdsExist($value)) {
                        $fail(__('One or more category tree IDs are invalid.'));
                    }
                },
            ],
        ];
    }

    /**
     * Recursively collect all IDs from the nested nodes array and verify they exist.
     *
     * @param  array<int, array{id?: mixed, children?: array<mixed>}>  $nodes
     */
    private function allIdsExist(array $nodes): bool
    {
        $ids = $this->collectIds($nodes);

        if (empty($ids)) {
            return true;
        }

        $existCount = CategoryTree::query()->whereIn('id', $ids)->count();

        return $existCount === count($ids);
    }

    /**
     * @param  array<int, array{id?: mixed, children?: array<mixed>}>  $nodes
     * @return array<int, int>
     */
    private function collectIds(array $nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            if (isset($node['id'])) {
                $ids[] = (int) $node['id'];
            }
            if (! empty($node['children'])) {
                $ids = array_merge($ids, $this->collectIds($node['children']));
            }
        }

        return array_unique($ids);
    }
}
