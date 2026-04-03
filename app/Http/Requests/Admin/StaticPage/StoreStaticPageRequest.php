<?php

namespace App\Http\Requests\Admin\StaticPage;

use App\Models\StaticPage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaticPageRequest extends FormRequest
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
        return [
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ((int) $value !== 0 && ! StaticPage::query()->whereKey($value)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                },
            ],
            'code' => ['required', 'string', 'max:255', Rule::unique('static_pages', 'code')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_no' => ['required', 'integer', 'min:0'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('static_pages', 'slug')],
            'is_active' => ['required', 'boolean'],
        ];
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
