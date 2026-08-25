@props(['post'])

<article class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full mb-2">
        {{ $post->categoria->nombre }}

    </span>
    <h2 class="text-xl font-semibold text-gray-900">{{ $post->titulo }}</h2>
    <p class="text-gray-600 mt-2">{{ Str::limit($post->contenido, 90) }}</p>
    {{ $post->created_at->format('d/m/Y') }}

</article>
