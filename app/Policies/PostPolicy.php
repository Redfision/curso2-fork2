<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Corre ANTES que los demas metodos (nivel 2).
     * true autoriza, false prohibe, null significa "no opino, que decidan los otros".
     * Regresar false aqui deja a TODOS sin permisos: es el error clasico.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->rol === 'admin') {
            return true;
        }

        return null;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Nivel 3: aqui no hay modelo todavia, por eso solo recibe el usuario.
    public function create(User $user): bool
    {
        return in_array($user->rol, ['admin', 'editor']);
    }
}
