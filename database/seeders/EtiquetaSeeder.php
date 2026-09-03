<?php

namespace Database\Seeders;

use App\Models\Etiqueta;
use Illuminate\Database\Seeder;

class EtiquetaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['seguridad', 'operativo', 'aviso', 'capacitación'] as $nombre) {
            Etiqueta::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
