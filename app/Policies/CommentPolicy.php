<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        // Cualquiera puede ver los comentarios.
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
        // Cualquiera puede ver un comentario específico.
        return true;
    }

    public function create(User $user): bool
    {
        // Cualquiera puede crear un comentario.
        return true;
    }

    public function update(User $user, Comment $comment): bool
    {
        // Solo el autor del comentario o un administrador puede actualizarlo.
        return $user->id === $comment->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Comment $comment): bool
    {
        // Solo el autor del comentario o un administrador puede eliminarlo.
        return $user->id === $comment->user_id || $user->role === 'admin';
    }
}
