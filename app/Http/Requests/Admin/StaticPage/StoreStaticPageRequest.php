<?php

namespace App\Http\Requests\Admin\StaticPage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates create payload for a {@see StaticPage}.
 */
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
