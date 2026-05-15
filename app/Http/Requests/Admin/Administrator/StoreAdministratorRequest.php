<?php

namespace App\Http\Requests\Admin\Administrator;

use App\Models\Administrator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validates create payload for a new {@see Administrator}.
 */
class StoreAdministratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active')
                ? $this->boolean('is_active')
                : false,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('administrators', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'is_active' => ['required', 'boolean'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'admin')],
        ];
    }
}
