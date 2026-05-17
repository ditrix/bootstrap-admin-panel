<?php

namespace App\Http\Requests\Admin\Administrator;

use App\Models\Administrator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validates updates to an administrator, including optional password rotation.
 */
class UpdateAdministratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('administrator') instanceof Administrator;
    }

    protected function prepareForValidation(): void
    {
        $pass = $this->input('password');
        if ($pass === null || $pass === '') {
            $this->merge(['password' => null]);
        }
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Administrator $admin */
        $admin = $this->route('administrator');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('administrators', 'email')->ignore($admin->getKey())],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'is_active' => ['required', 'boolean'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'admin')],
        ];
    }
}
