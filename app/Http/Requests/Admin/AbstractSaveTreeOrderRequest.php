<?php

namespace App\Http\Requests\Admin;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base validation for nested tree reorder payloads.
 *
 * Subclasses bind a concrete Eloquent model via {@see modelClass()} and provide
 * a localized error message via {@see invalidIdsMessage()}.
 */
abstract class AbstractSaveTreeOrderRequest extends FormRequest
{
    /**
     * The fully-qualified Eloquent model class whose IDs are validated.
     *
     * @return class-string
     */
    abstract protected function modelClass(): string;

    abstract protected function invalidIdsMessage(): string;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nodes' => [
                'required',
                'array',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! is_array($value) || ! $this->allIdsExist($value)) {
                        $fail($this->invalidIdsMessage());
                    }
                },
            ],
        ];
    }

    /**
     * Recursively collect all IDs from the nested nodes array and verify they exist.
     *
     * @param  array<int, array{id?: mixed, children?: array<mixed>}>  $nodes
     */
    private function allIdsExist(array $nodes): bool
    {
        $ids = $this->collectIds($nodes);

        if (empty($ids)) {
            return true;
        }

        $class = $this->modelClass();
        $existCount = $class::query()->whereIn('id', $ids)->count();

        return $existCount === count($ids);
    }

    /**
     * @param  array<int, array{id?: mixed, children?: array<mixed>}>  $nodes
     * @return array<int, int>
     */
    private function collectIds(array $nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            if (isset($node['id'])) {
                $ids[] = (int) $node['id'];
            }
            if (! empty($node['children'])) {
                $ids = array_merge($ids, $this->collectIds($node['children']));
            }
        }

        return array_unique($ids);
    }
}
