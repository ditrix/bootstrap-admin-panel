<?php

namespace App\Http\Requests\Admin\CategoryTree;

use App\Models\CategoryTree;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates inline updates to an existing category node (scoped to route-bound model).
 */
class UpdateCategoryTreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('category_tree') instanceof CategoryTree;
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug');
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'slug' => ($slug === '' || $slug === null) ? null : $slug,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CategoryTree $categoryTree */
        $categoryTree = $this->route('category_tree');

        return [
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail) use ($categoryTree): void {
                    $pid = (int) $value;
                    if ($pid !== 0 && ! CategoryTree::query()->whereKey($pid)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                    if ($pid === $categoryTree->getKey()) {
                        $fail(__('A category node cannot be its own parent.'));
                    }
                },
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('category_trees', 'slug')->ignore($categoryTree->getKey()),
            ],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var CategoryTree $categoryTree */
            $categoryTree = $this->route('category_tree');
            $parentId = (int) $this->input('parent_id');
            if ($parentId === 0) {
                return;
            }
            if ($this->wouldCreateParentCycle($categoryTree, $parentId)) {
                $validator->errors()->add('parent_id', __('This parent would create a cycle in the hierarchy.'));
            }
        });
    }

    private function wouldCreateParentCycle(CategoryTree $node, int $newParentId): bool
    {
        $current = CategoryTree::query()->find($newParentId);
        while ($current !== null) {
            if ($current->getKey() === $node->getKey()) {
                return true;
            }
            if ((int) $current->parent_id === 0) {
                return false;
            }
            $current = $current->parent;
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.unique' => __('A category with this slug already exists.'),
        ];
    }
}
