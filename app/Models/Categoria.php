<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Categoria.php
class Categoria extends Model
{
    protected $fillable = ['nombre'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

