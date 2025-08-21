<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'nullable'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
