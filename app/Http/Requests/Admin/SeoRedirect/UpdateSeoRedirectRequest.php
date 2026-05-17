<?php

namespace App\Http\Requests\Admin\SeoRedirect;

use App\Models\SeoRedirect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates updates to an existing {@see SeoRedirect} route model.
 */
class UpdateSeoRedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('seo_redirect') instanceof SeoRedirect;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'slug_from' => is_string($this->input('slug_from')) ? ltrim($this->input('slug_from'), '/') : $this->input('slug_from'),
            'slug_to' => is_string($this->input('slug_to')) ? trim((string) $this->input('slug_to')) : $this->input('slug_to'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var SeoRedirect $redirect */
        $redirect = $this->route('seo_redirect');

        return [
            'slug_from' => [
                'required',
                'string',
                'max:768',
                'regex:/^[a-zA-Z0-9_\/\-]+$/',
                Rule::unique('seo_redirects', 'slug_from')->ignore($redirect->getKey()),
            ],
            'slug_to' => ['required', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug_from.unique' => __('A redirect for this path already exists.'),
        ];
    }
}
