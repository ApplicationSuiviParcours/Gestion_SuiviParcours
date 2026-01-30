<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Note;
use App\Models\Bulletin;
use App\Models\Matiere;
use App\Models\Evaluation;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $bulletins = Bulletin::all();
        $matieres = Matiere::all();
        $evaluations = Evaluation::all();

        if ($bulletins->isEmpty() || $matieres->isEmpty() || $evaluations->isEmpty()) {
            $this->command->warn('❌ Données manquantes pour générer les notes (bulletins, matières ou évaluations)');
            return;
        }

        foreach ($bulletins as $bulletin) {
            $classeId = $bulletin->classe_id;

            foreach ($matieres as $matiere) {
                // Récupérer une évaluation existante pour cette matière et cette classe
                $evaluation = $evaluations
                    ->where('matiere_id', $matiere->id)
                    ->where('classe_id', $classeId)
                    ->first();

                if (!$evaluation) {
                    // Sauter si aucune évaluation pour cette matière et cette classe
                    continue;
                }

                // Générer une note aléatoire
                $valeur = rand(8, 18);
                $coefficient = rand(1, 5);

                // Créer la note
                Note::create([
                    'bulletin_id'   => $bulletin->id,
                    'eleve_id'      => $bulletin->eleve_id,
                    'matiere_id'    => $matiere->id,
                    'evaluation_id' => $evaluation->id,
                    'valeur'        => $valeur,
                    'coefficient'   => $coefficient,
                ]);
            }
        }

        $this->command->info('✅ Toutes les notes ont été générées avec succès');
    }
}
