<?php

namespace App\Services;

use App\Models\Bulletin;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Note;
use App\Models\Evaluation;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinService
{
    /**
     * Génère les bulletins pour tous les élèves
     */
    public function genererBulletins(): void
    {
        $eleves = Eleve::all();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $annee = AnneeScolaire::latest()->first();
        $evaluations = Evaluation::all();

        foreach ($eleves as $eleve) {
            $classe = $eleve->classe ?? $classes->random();

            // Création ou récupération du bulletin
            $bulletin = Bulletin::firstOrCreate([
                'eleve_id' => $eleve->id,
                'classe_id' => $classe->id,
                'annee_id' => $annee->id,
                'periode' => 'Trimestre 1',
            ]);

            $totalPoints = 0;
            $totalCoef = 0;

            foreach ($matieres as $matiere) {
                // Récupération d'une évaluation pour cette matière/classe
                $evaluation = $evaluations
                    ->where('matiere_id', $matiere->id)
                    ->where('classe_id', $classe->id)
                    ->first();

                if (!$evaluation) {
                    // Si aucune évaluation trouvée, on saute
                    continue;
                }

                $valeur = rand(8, 18); // Note aléatoire
                $coef = rand(1, 5);    // Coefficient aléatoire

                // Création ou mise à jour de la note
                Note::updateOrCreate(
                    [
                        'bulletin_id' => $bulletin->id,
                        'eleve_id' => $eleve->id,
                        'evaluation_id' => $evaluation->id,
                    ],
                    [
                        'matiere_id' => $matiere->id,
                        'valeur' => $valeur,
                        'coefficient' => $coef,
                    ]
                );

                $totalPoints += $valeur * $coef;
                $totalCoef += $coef;
            }

            // Calcul de la moyenne
            $moyenne = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : 0;

            // Mise à jour du bulletin
            $bulletin->update([
                'moyenne' => $moyenne,
                'appreciation' => $this->appreciation($moyenne),
            ]);
        }
    }

    /**
     * Génère le PDF d’un bulletin spécifique
     */
    public function genererPDF(Bulletin $bulletin)
    {
        // Charger toutes les notes avec matière et évaluation
        $bulletin->load('notes.matiere', 'notes.evaluation', 'eleve', 'classe', 'annee');

        $pdf = Pdf::loadView('pdf.bulletin', compact('bulletin'));

        // Nom du fichier : Bulletin_NomEleve.pdf
        return $pdf->download("Bulletin_{$bulletin->eleve->nom}.pdf");
    }

    /**
     * Retourne l'appréciation selon la moyenne
     */
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
