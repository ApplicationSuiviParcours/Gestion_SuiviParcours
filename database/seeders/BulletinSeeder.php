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
        $eleves      = Eleve::with('classe')->get();
        $classes     = Classe::all();
        $matieres    = Matiere::all();
        $annee       = AnneeScolaire::latest()->first();

        if ($eleves->isEmpty() || $classes->isEmpty() || $matieres->isEmpty() || !$annee) {
            $this->command->warn('❌ Données manquantes pour générer les bulletins');
            return;
        }

        foreach ($eleves as $eleve) {

            // Classe réelle de l'élève ou aléatoire
            $classe = $eleve->classe ?? $classes->random();

            // Création ou mise à jour du bulletin
            $bulletin = Bulletin::updateOrCreate(
                [
                    'eleve_id'  => $eleve->id,
                    'classe_id' => $classe->id,
                    'annee_id'  => $annee->id,
                    'periode'   => 'Trimestre 1',
                ],
                [
                    'moyenne'      => 0,
                    'rang'         => null,
                    'appreciation' => null,
                ]
            );

            $totalPoints = 0;
            $totalCoef   = 0;

            foreach ($matieres as $matiere) {

                // Évaluation liée à la matière et à la classe
                $evaluation = Evaluation::where('matiere_id', $matiere->id)
                    ->where('classe_id', $classe->id)
                    ->first();

                if (!$evaluation) {
                    continue;
                }

                $noteValeur = rand(8, 18);
                $coef       = rand(1, 5);

                Note::updateOrCreate(
                    [
                        'bulletin_id'   => $bulletin->id,
                        'eleve_id'      => $eleve->id,
                        'evaluation_id' => $evaluation->id,
                    ],
                    [
                        'matiere_id'  => $matiere->id,
                        'valeur'      => $noteValeur,
                        'coefficient' => $coef,
                    ]
                );

                $totalPoints += $noteValeur * $coef;
                $totalCoef   += $coef;
            }

            // Calcul de la moyenne
            $moyenne = $totalCoef > 0
                ? round($totalPoints / $totalCoef, 2)
                : 0;

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
            default        => 'Insuffisant',
        };
    }
}
