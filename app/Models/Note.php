<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Eleve;
use App\Models\Bulletin;
use App\Models\Matiere;
use App\Models\Evaluation;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'eleve_id',
        'matiere_id',
        'valeur',
        'coefficient',
        'bulletin_id',
    ];

    // Une note appartient à un bulletin
    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class);
    }

    // Relation avec l'élève
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    // Relation avec la matière
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    // Relation avec l'évaluation
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
