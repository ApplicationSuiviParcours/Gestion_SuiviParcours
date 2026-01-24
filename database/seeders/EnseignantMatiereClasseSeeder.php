<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnseignantMatiereClasse;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Classe;


class EnseignantMatiereClasseSeeder extends Seeder
{
    public function run(): void
    {
      

        $enseignants = Enseignant::all();
        $matieres = Matiere::all();
        $classes = Classe::all();

        foreach ($enseignants as $enseignant) {
            foreach ($classes as $classe) {
                $matiere = $matieres->random();

                EnseignantMatiereClasse::firstOrCreate([
                    'enseignant_id' => $enseignant->id,
                    'matiere_id' => $matiere->id,
                    'classe_id' => $classe->id,
                ]);
            }
        }
    }
}
