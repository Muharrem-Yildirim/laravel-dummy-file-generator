<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileGenerateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_size' => ['required', 'integer', 'between:1,1000000000', function ($attribute, $value, $fail) {
                if ($value % 1024 != 0) {
                    $fail('The file size must be a multiple of 1024');
                }
            }],
            // 'file_name' => ['required', 'string', 'max:255'],
        ];
    }
}
