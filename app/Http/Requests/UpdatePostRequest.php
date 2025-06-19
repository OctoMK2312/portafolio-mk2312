<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtenemos el post de la ruta para usar su ID
        $post = $this->route('post');

        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'sometimes|in:draft,published',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Regla unique para el slug que ignora el post actual
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('posts')->ignore($post->id),
            ],
        ];
    }
}