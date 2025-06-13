<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Post::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'sometimes|in:draft,published',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'content.required' => 'El contenido es obligatorio.',
            'category_id.required' => 'La categoría es obligatoria.',
            'status.in' => 'El estado debe ser uno de los siguientes: draft, published.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'content.string' => 'El contenido debe ser una cadena de texto.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'featured_image.image' => 'La imagen destacada debe ser una imagen.',
            'featured_image.mimes' => 'La imagen destacada debe ser de tipo jpeg, png, jpg, gif o webp.',
            'featured_image.max' => 'La imagen destacada no puede exceder los 2 MB.',
        ];
    }
}
