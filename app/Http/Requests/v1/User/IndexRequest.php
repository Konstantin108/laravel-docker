<?php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'nullable'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'nullable'],
            'page' => ['sometimes', 'integer', 'min:1', 'nullable'],
        ];
    }
}
