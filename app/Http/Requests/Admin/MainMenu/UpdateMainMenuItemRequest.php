<?php

namespace App\Http\Requests\Admin\MainMenu;

use App\Models\MainMenuItem;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMainMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('main_menu_item') instanceof MainMenuItem;
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
        /** @var MainMenuItem $item */
        $item = $this->route('main_menu_item');

        return [
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail) use ($item): void {
                    $pid = (int) $value;
                    if ($pid !== 0 && ! MainMenuItem::query()->whereKey($pid)->exists()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                    if ($pid === $item->getKey()) {
                        $fail(__('A menu item cannot be its own parent.'));
                    }
                },
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('main_menu_items', 'slug')->ignore($item->getKey()),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var MainMenuItem $item */
            $item = $this->route('main_menu_item');
            $all = MainMenuItem::all()->keyBy('id');
            if ($all->isEmpty()) {
                return;
            }
            $parentId = (int) $this->input('parent_id');
            if ($parentId !== 0 && $this->wouldCreateParentCycle($item, $parentId, $all)) {
                $v->errors()->add('parent_id', __('This parent would create a cycle in the hierarchy.'));

                return;
            }
        });
    }

    /**
     * @param  Collection<int, MainMenuItem>  $all
     */
    private function wouldCreateParentCycle(MainMenuItem $node, int $newParentId, Collection $all): bool
    {
        $current = $all->get($newParentId);
        while ($current !== null) {
            if ($current->getKey() === $node->getKey()) {
                return true;
            }
            if ((int) $current->parent_id === 0) {
                return false;
            }
            $current = $all->get((int) $current->parent_id);
        }

        return false;
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
