<?php

namespace App\Http\Requests\Admin\MainMenu;

use App\Models\MainMenuItem;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMainMenuItemRequest extends FormRequest
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
                    if ($pid !== 0 && ! MainMenuItem::query()->whereKey($pid)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                },
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('main_menu_items', 'slug'),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $all = MainMenuItem::all()->keyBy('id');
            $newParentId = (int) $this->input('parent_id');
            $newNodeDepth = $newParentId === 0
                ? 1
                : $this->absoluteDepthFromRoot($newParentId, $all) + 1;
            if ($newNodeDepth > 3) {
                $v->errors()->add('parent_id', __('The main menu may have at most :n levels.', ['n' => 3]));
            }
        });
    }

    /**
     * @param  Collection<int, MainMenuItem>  $all
     */
    private function absoluteDepthFromRoot(int $id, $all): int
    {
        $depth = 0;
        $cur = $id;
        $guard = 0;
        while ($cur > 0 && $guard++ < 100) {
            $depth++;
            $m = $all->get($cur);
            if (! $m) {
                return $depth;
            }
            $pid = (int) $m->parent_id;
            if ($pid === 0) {
                return $depth;
            }
            $cur = $pid;
        }

        return $depth;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.unique' => __('A menu item with this slug already exists.'),
        ];
    }
}
