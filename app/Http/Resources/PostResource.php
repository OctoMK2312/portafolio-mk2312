<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'featured_image_url' => $this->featured_image_url,
            'allow_comments' => $this->allow_comments,
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'last_name' => $this->user->last_name,
                'username' => $this->user->username,
            ],
            'category' => new CategoryResource($this->whenLoaded('category')), // Reutilizamos el resource de categoría
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}