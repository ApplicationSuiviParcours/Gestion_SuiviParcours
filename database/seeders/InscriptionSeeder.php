<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inscription;

class InscriptionSeeder extends Seeder
{
    public function run()
    {
        Inscription::create([
            'eleve_id' => 1,
            'classe_id' => 1,
            'annee_id' => 1,
            'date_inscription' => now(),
            'statut' => 'actif',
        ]);
    }
}
