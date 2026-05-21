<?php

namespace App\Http\Requests\v1\Product;

use App\Enums\SortedByEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<Enum|string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'nullable'],
            'sorted_by' => ['sometimes', 'string', Rule::enum(SortedByEnum::class)],
            'limit' => ['sometimes', 'integer', 'min:1', 'nullable'],
        ];
    }
}
