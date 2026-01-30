<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\AnneeScolaire;

class EvaluationSeeder extends Seeder
{
    public function run()
    {
        $types = ['devoir', 'composition', 'examen'];
        $classes = Classe::all();
        $matieres = Matiere::all();
        $annees = AnneeScolaire::all();

        if ($classes->isEmpty() || $matieres->isEmpty() || $annees->isEmpty()) {
            $this->command->warn('Données manquantes : classes, matières ou années');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            Evaluation::create([
                'type_evaluation' => $types[array_rand($types)],
                'date_evaluation' => now()->subDays(rand(0, 30)), 
                'classe_id' => $classes->random()->id,
                'matiere_id' => $matieres->random()->id,
                'annee_id' => $annees->random()->id,
            ]);
        }
    }
}
