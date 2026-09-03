<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['titulo', 'contenido', 'categoria_id', 'publicado', 'user_id'];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    // ---- Relaciones ----

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class);
    }

    // ---- Scopes ----

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeDeCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }
}
