<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bulletin;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\AnneeScolaire;
use App\Models\Evaluation;

class BulletinSeeder extends Seeder
{
    public function run(): void
    {
        $eleves   = Eleve::all();
        $classes  = Classe::all();
        $matieres = Matiere::all();
        $annee    = AnneeScolaire::latest()->first();
        $evaluations = Evaluation::all();

        if ($eleves->isEmpty() || $classes->isEmpty() || $matieres->isEmpty() || !$annee || $evaluations->isEmpty()) {
            $this->command->warn('❌ Données manquantes pour générer les bulletins (élèves, classes, matières, évaluations ou année)');
            return;
        }

        foreach ($eleves as $eleve) {

            // On prend la vraie classe de l'élève si disponible, sinon random
            $classe = $eleve->classe_id ? Classe::find($eleve->classe_id) : $classes->random();

            // Création du bulletin
            $bulletin = Bulletin::create([
                'eleve_id'   => $eleve->id,
                'classe_id'  => $classe->id,
                'annee_id'   => $annee->id,
                'periode'    => 'Trimestre 1',
                'moyenne'    => 0, // calculée après
                'rang'       => null,
                'appreciation' => null,
            ]);

            $totalPoints = 0;
            $totalCoef = 0;

            foreach ($matieres as $matiere) {
                // On récupère une évaluation pour la matière et la classe
                $evaluation = $evaluations
                    ->where('matiere_id', $matiere->id)
                    ->where('classe_id', $classe->id)
                    ->first();

                if (!$evaluation) {
                    // Si aucune évaluation pour cette matière/classe, on saute
                    continue;
                }

                $noteValeur = rand(8, 18); // valeur de la note
                $coef = rand(1, 5); // coefficient aléatoire

                Note::create([
                    'bulletin_id'   => $bulletin->id,
                    'eleve_id'      => $eleve->id,
                    'matiere_id'    => $matiere->id,
                    'evaluation_id' => $evaluation->id,
                    'valeur'        => $noteValeur,
                    'coefficient'   => $coef,
                ]);

                $totalPoints += $noteValeur * $coef;
                $totalCoef += $coef;
            }

            // Calcul de la moyenne
            $moyenne = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : 0;

            $bulletin->update([
                'moyenne'      => $moyenne,
                'appreciation' => $this->appreciation($moyenne),
            ]);
        }

        $this->command->info('✅ Bulletins et notes générés avec succès');
    }

    private function appreciation(float $moyenne): string
    {
        return match (true) {
            $moyenne >= 16 => 'Excellent',
            $moyenne >= 14 => 'Très bien',
            $moyenne >= 12 => 'Bien',
            $moyenne >= 10 => 'Passable',
            default => 'Insuffisant',
        };
    }
}
