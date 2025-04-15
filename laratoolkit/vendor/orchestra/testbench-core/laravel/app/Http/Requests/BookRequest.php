<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class BookRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->getModelId();

        if ($this->isUpdate()) {
            // Update validation
            return [
                // 'name' => ['required', 'min:2'],
                // 'slug' => ['required', 'min:2', "unique:books,slug,$id"]
            ];
        }

        // Create validation
        return [
            // 'name' => ['required', 'min:2'],
            // 'slug' => ['required', 'unique:books,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'name.required' => 'The name is required.',
            // 'name.min' => 'The name must be at least 2 characters.',
        ];
    }
}
