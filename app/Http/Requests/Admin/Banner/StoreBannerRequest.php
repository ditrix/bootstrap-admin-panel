<?php

namespace App\Http\Requests\Admin\Banner;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates create payload for a {@see Banner}.
 */
class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');
        $normalizedCode = ($code !== null && is_string($code) && trim($code) === '') ? null : $code;

        $this->merge([
            'code' => $normalizedCode,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('banners', 'code')->whereNull('deleted_at'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'sort_no' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'banner_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.unique' => __('A banner with this code already exists.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadForModel(): array
    {
        return collect($this->validated())->except(['banner_image'])->all();
    }
}
