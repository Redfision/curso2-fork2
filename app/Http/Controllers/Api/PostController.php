<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(
            Post::publicados()->with(['categoria', 'user'])->latest()->paginate(10)
        );
    }

    public function show(Post $post)
    {
        return new PostResource($post->load(['categoria', 'user']));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Post::class);

        $datos = $request->validate([
            'titulo' => ['required', 'max:120'],
            'contenido' => ['required'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ]);

        $datos['user_id'] = $request->user()->id;

        $post = Post::create($datos);

        return (new PostResource($post->load(['categoria', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $datos = $request->validate([
            'titulo' => ['required', 'max:120'],
            'contenido' => ['required'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ]);

        $post->update($datos);

        return new PostResource($post->load(['categoria', 'user']));
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return response()->noContent();
    }
}
