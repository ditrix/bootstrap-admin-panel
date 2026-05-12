<?php

namespace App\Http\Requests\Admin\Banner;

use App\Models\Banner;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates update payload for a {@see Banner} excluding invalid parent recursion.
 */
class UpdateBannerRequest extends FormRequest
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
        $banner = $this->route('banner');
        \assert($banner instanceof Banner);

        return [
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail) use ($banner): void {
                    $pid = (int) $value;
                    if ($pid !== 0 && ! Banner::query()->whereKey($pid)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                    if ($pid === $banner->getKey()) {
                        $fail(__('The banner cannot be its own parent.'));
                    }
                },
            ],
            'code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('banners', 'code')
                    ->ignore($banner->id)
                    ->whereNull('deleted_at'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'sort_no' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'banner_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Banner $banner */
            $banner = $this->route('banner');
            $parentId = (int) $this->input('parent_id');
            if ($parentId === 0) {
                return;
            }
            if ($this->wouldCreateParentCycle($banner, $parentId)) {
                $validator->errors()->add('parent_id', __('This parent would create a cycle in the hierarchy.'));
            }
        });
    }

    private function wouldCreateParentCycle(Banner $banner, int $newParentId): bool
    {
        $current = Banner::query()->find($newParentId);
        while ($current !== null) {
            if ($current->getKey() === $banner->getKey()) {
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
