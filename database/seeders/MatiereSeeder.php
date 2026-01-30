<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatiereSeeder extends Seeder
{
    public function run()
    {
        $matieres = ['Mathématiques', 'Français', 'Anglais', 'Physique', 'Chimie', 'Histoire', 'Géographie', 'Informatique'];

        foreach ($matieres as $libelle) {
            Matiere::create([
                'libelle' => $libelle,
                'coefficient' => rand(1, 5),
            ]);
        }
    }
}
