<?php

namespace App\Http\Requests\Admin\StaticPage;

use App\Models\StaticPage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateStaticPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $staticPage = $this->route('static_page');
        \assert($staticPage instanceof StaticPage);

        return [
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail) use ($staticPage): void {
                    $pid = (int) $value;
                    if ($pid !== 0 && ! StaticPage::query()->whereKey($pid)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                    if ($pid === $staticPage->getKey()) {
                        $fail(__('The page cannot be its own parent.'));
                    }
                },
            ],
            'code' => ['required', 'string', 'max:255', Rule::unique('static_pages', 'code')->ignore($staticPage->id)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_no' => ['required', 'integer', 'min:0'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('static_pages', 'slug')->ignore($staticPage->id)],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var StaticPage $staticPage */
            $staticPage = $this->route('static_page');
            $parentId = (int) $this->input('parent_id');
            if ($parentId === 0) {
                return;
            }
            if ($this->wouldCreateParentCycle($staticPage, $parentId)) {
                $validator->errors()->add('parent_id', __('This parent would create a cycle in the hierarchy.'));
            }
        });
    }

    private function wouldCreateParentCycle(StaticPage $page, int $newParentId): bool
    {
        $current = StaticPage::query()->find($newParentId);
        while ($current !== null) {
            if ($current->getKey() === $page->getKey()) {
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
            'code.unique' => __('A page with this code already exists.'),
            'slug.unique' => __('A page with this slug already exists.'),
        ];
    }
}
