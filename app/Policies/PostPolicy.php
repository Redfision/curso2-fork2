<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Corre ANTES que los demas metodos, pero SOLO si el metodo existe:
     * en el blog publico, Gate::authorize con un metodo ausente niega sin
     * llegar aqui. En el panel, Filament ni siquiera consulta la Policy si
     * el metodo falta: permite. Por eso conviene escribirlos todos.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->rol === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->rol, ['admin', 'editor']);
    }

    // Decide, fila por fila, si aparece el boton "Editar".
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Decide, fila por fila, si aparece el boton "Borrar".
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // El borrado masivo de la tabla pregunta esto, no delete(). Sin este
    // metodo, el editor podia seleccionar todos los avisos y borrarlos.
    public function deleteAny(User $user): bool
    {
        return false;   // nadie en masa; el admin pasa por before()
    }
}
