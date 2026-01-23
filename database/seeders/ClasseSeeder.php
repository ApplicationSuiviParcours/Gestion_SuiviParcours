<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;

class ClasseSeeder extends Seeder
{
    public function run()
    {
        Classe::create([
            'nom_classe' => 'CP1',
            'niveau' => 'primaire',
            'filiere' => null,
            'effectif_max' => 30,
        ]);

        Classe::create([
            'nom_classe' => '6ème A',
            'niveau' => 'college',
            'filiere' => null,
            'effectif_max' => 35,
        ]);

        Classe::create([
            'nom_classe' => 'Terminale S',
            'niveau' => 'lycee',
            'filiere' => 'Scientifique',
            'effectif_max' => 40,
        ]);
    }
}