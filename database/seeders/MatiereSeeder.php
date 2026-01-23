<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatiereSeeder extends Seeder
{
    public function run()
    {
        Matiere::create([
            'libelle' => 'Mathématiques',
            'coefficient' => 5,
        ]);

        Matiere::create([
            'libelle' => 'Français',
            'coefficient' => 4,
        ]);
    }
}
