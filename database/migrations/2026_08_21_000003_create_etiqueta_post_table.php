<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convención del pivote: nombres de los dos modelos en singular,
        // en orden alfabético: "etiqueta" antes que "post".
        Schema::create('etiqueta_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('etiqueta_id')->constrained('etiquetas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etiqueta_post');
    }
};
