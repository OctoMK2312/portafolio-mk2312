<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Comment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del comentario es obligatorio.',
            'content.string' => 'El contenido debe ser una cadena de texto.',
            'content.max' => 'El contenido no puede exceder los 1000 caracteres.',
            'parent_id.exists' => 'El comentario padre especificado no existe.',
        ];
    }
}
