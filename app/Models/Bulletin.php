<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\AnneeScolaire;
use App\Models\Note;

class Bulletin extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_id',
        'periode',
        'moyenne',
        'rang',
        'appreciation',
    ];

    public function eleve(): BelongsTo {
        return $this->belongsTo(Eleve::class);
    }

    public function classe(): BelongsTo {
        return $this->belongsTo(Classe::class);
    }

    public function notes(): HasMany {
        return $this->hasMany(Note::class);
    }

    public function annee(): BelongsTo 
    { 
        return $this->belongsTo(AnneeScolaire::class); 
    }

    
    // 🔹 Méthode pour recalculer la moyenne automatiquement
    public function recalculerMoyenne(): void
    {
        $notes = $this->notes;

        if ($notes->count() === 0) {
            $this->update(['moyenne' => 0]);
            return;
        }

        $totalPoints = 0;
        $totalCoef = 0;

        foreach ($notes as $note) {
            $totalPoints += $note->valeur * $note->coefficient;
            $totalCoef += $note->coefficient;
        }

        $moyenne = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : 0;

        $this->update([
            'moyenne' => $moyenne,
            'appreciation' => $this->appreciation($moyenne),
        ]);
    }

    // 🔹 Appréciation automatique selon la moyenne
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

 
