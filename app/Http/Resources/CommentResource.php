<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "post_id" => $this->post_id,
            "user_id" => $this->user_id,
            "content" => $this->content,
            "parent_id" => $this->parent_id,
            "created_at" => $this->created_at->toIso8601String(),
            "updated_at" => $this->updated_at->toIso8601String(),
            "user" => [
                "id" => $this->user->id,
                "name" => $this->user->name,
                "last_name" => $this->user->last_name,
                "username" => $this->user->username,
            ],
            "children" => CommentResource::collection($this->whenLoaded('children')),
            "parent" => new CommentResource($this->whenLoaded('parent')),
        ];
    }
}
