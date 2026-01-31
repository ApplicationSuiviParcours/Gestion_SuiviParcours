<?php

namespace App\Services;

use App\Models\Bulletin;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Note;
use App\Models\AnneeScolaire;
use App\Models\Evaluation;
use App\Models\ClasseMatiere;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinService
{
    public function genererBulletins(): void
    {
        $eleves = Eleve::all();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $annee = AnneeScolaire::latest()->first();
        $evaluations = Evaluation::all();

        foreach ($eleves as $eleve) {
            $classe = $eleve->classe ?? $classes->random();

            // Création du bulletin
            $bulletin = Bulletin::firstOrCreate(
                [
                    'eleve_id' => $eleve->id,
                    'classe_id' => $classe->id,
                    'annee_id' => $annee->id,
                    'periode' => 'Trimestre 1',
                ]
            );

            $totalPoints = 0;
            $totalCoef = 0;

            foreach ($matieres as $matiere) {
                // Récupération du coefficient depuis classe_matiere
                $classeMatiere = ClasseMatiere::where('classe_id', $classe->id)
                    ->where('matiere_id', $matiere->id)
                    ->first();

                if (!$classeMatiere) {
                    // Si pas de coefficient défini, on passe
                    continue;
                }

                // On récupère toutes les évaluations de la matière pour la classe
                $matiereEvaluations = $evaluations
                    ->where('matiere_id', $matiere->id)
                    ->where('classe_id', $classe->id);

                foreach ($matiereEvaluations as $evaluation) {
                    $noteValeur = rand(8, 18); // valeur aléatoire
                    $coef = $classeMatiere->coefficient;

                    Note::updateOrCreate(
                        [
                            'bulletin_id' => $bulletin->id,
                            'eleve_id' => $eleve->id,
                            'evaluation_id' => $evaluation->id,
                        ],
                        [
                            'matiere_id' => $matiere->id,
                            'valeur' => $noteValeur,
                            'coefficient' => $coef,
                        ]
                    );

                    $totalPoints += $noteValeur * $coef;
                    $totalCoef += $coef;
                }
            }

            // Calcul de la moyenne
            $moyenne = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : 0;

            $bulletin->update([
                'moyenne' => $moyenne,
                'appreciation' => $this->appreciation($moyenne),
            ]);
        }
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

    public function genererPDF(Bulletin $bulletin)
    {
        $pdf = Pdf::loadView('pdf.bulletin', compact('bulletin'));
        return $pdf->download("Bulletin_{$bulletin->eleve->nom}.pdf");
    }
}
