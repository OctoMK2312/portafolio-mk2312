<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('category'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')->id;

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categorys,slug,' . $categoryId,
            'parent_id' => 'nullable|exists:categorys,id',
        ];
    }

    /**
     * Get the custom validation messages.
     * 
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'El slug debe ser único.',
            'parent_id.exists' => 'La categoría padre debe existir.',
        ];
    }

}