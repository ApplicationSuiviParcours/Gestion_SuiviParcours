<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\ClasseMatiere;

class ClasseMatiereSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classe::all();
        $matieres = Matiere::all();

        foreach ($classes as $classe) {
            foreach ($matieres as $matiere) {
                ClasseMatiere::updateOrCreate(
                    [
                        'classe_id' => $classe->id,
                        'matiere_id' => $matiere->id,
                    ],
                    [
                        'coefficient' => rand(1, 5),
                    ]
                );
            }
        }

        $this->command->info('✅ Classe-Matière et coefficients générés.');
    }
}
