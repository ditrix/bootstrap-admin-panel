<?php

namespace App\Http\Requests\Admin\CategoryTree;

use App\Models\CategoryTree;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates create payload for a {@see CategoryTree} node (slug uniqueness).
 */
class StoreCategoryTreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
        return [
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $pid = (int) $value;
                    if ($pid !== 0 && ! CategoryTree::query()->whereKey($pid)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                },
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('category_trees', 'slug'),
            ],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
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
